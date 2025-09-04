<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCatalogRequest;
use App\Http\Resources\CatalogResource;
use App\Models\Catalog;
use App\Models\Basket;
use App\Models\Template;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Product;



class CatalogController extends Controller
{
    use AuthorizesRequests;
     // Get all catalogs
    public function index()
    {
        $catalogs = Catalog::with(['basket'])->latest()->get();
        $data = CatalogResource::collection($catalogs);
        return response()->json([
            'message' => 'Catalogs Retrieved Successfully',
            'data' => $data
        ], 200);
    }

    // Store a new catalog
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'basket_id' => 'required|exists:baskets,id',
            'template_id' => 'required|exists:templates,id',
        ]);

        $basket = Basket::with('products')->findOrFail($validated['basket_id']);
        $template = Template::findOrFail($validated['template_id']);

        // تأكد إن view PDF موجود
        if (!view()->exists('pdf.templates.custom_template')) {
            return response()->json(['error' => 'PDF view not found'], 404);
        }

        // Generate the PDF
        $pdf = Pdf::loadView('pdf.templates.custom_template', [
            'basket' => $basket,
            'template' => $template,
        ]);

        // Save PDF file
        $pdfPath = 'catalogs/' . Str::uuid() . '.pdf';
        Storage::disk('public')->put($pdfPath, $pdf->output());

        // Save catalog data
        $catalog = Catalog::create([
            'name' => $validated['name'],
            'basket_id' => $validated['basket_id'],
            'template_id' => $validated['template_id'],
            'created_by' => Auth::id(),
            'pdf_path' => $pdfPath,
        ]);
        $data = new CatalogResource($catalog);
        return response()->json([
            'message' => 'Catalog Created Successfully',
            'data' => $data
        ], 201);
    }

    // Show single catalog
    public function show(Catalog $catalog)
    {
        $catalog->load('basket.basketProducts.product', 'template', 'creator');
        $data = new CatalogResource($catalog);
        return response()->json([
            'message' => 'Catalog Retrieved Successfully',
            'data' => $data
        ], 200);
    }
   public function generateCatalog(Request $request)
{
    $request->validate([
        'template_id' => 'required|exists:templates,id',
        'products' => 'required|array',
        'products.*' => 'exists:products,id',
    ]);

    $template = Template::with('client', 'creator')->findOrFail($request->template_id);
    $user = Auth::user();
    $products = Product::whereIn('id', $request->products)->get();

    // إنشاء الـ Catalog في الداتابيز (مؤقتاً)
    $catalog = Catalog::create([
        'title' => 'My Custom Catalog',
        'template_id' => $template->id,
        'created_by' => $user->id,
        'basket_id' => $request->basket_id, // لو مش مستخدم باسكت
    ]);

    // الآن استخدم نفس الفيو المستخدم في التمبليت
    // ولازم تعدل View `templates.pdf` ليقبل collection من Product بدل TemplateProducts
    $client = $template->client;
    $templateProducts = $products; // تمرير الـ products الجاية من المستخدم

    $pdf = Pdf::loadView('templates.pdf', [
        'template' => $template,
        'user' => $user,
        'client' => $client,
        'templateProducts' => $templateProducts
    ])->setPaper('A4', 'portrait');

    // حفظ الملف
    $filename = 'catalog_' . time() . '.pdf';
    $filePath = 'catalogs/' . $filename;
    Storage::disk('public')->put($filePath, $pdf->output());

    // حفظ المسار داخل الكاتالوج
    $catalog->pdf_path = $filePath;
    $catalog->save();

    return response()->json([
        'message' => 'Catalog PDF Generated Successfully',
        'file_url' => Storage::url($filePath),
    ]);
}

public function convertToCatalog(Request $request, Basket $basket)
{
    $request->validate([
        'template_id' => 'required|exists:templates,id',
        'name' => 'required|string|max:255',
    ]);

    // Check if basket already converted
    if ($basket->status === 'converted') {
        return response()->json([
            'message' => 'This basket has already been converted to a catalog.',
        ], 400);
    }

    // تحميل التمبليت مع العلاقات المطلوبة
    $template = Template::with(['client', 'creator'])->findOrFail($request->template_id);
    $user = Auth::user();

    // تحميل المنتجات من الباسكت
    $basketProducts = $basket->basketProducts()->with('product')->get();

    // تحويل basketProducts إلى نفس شكل templateProducts
    $templateProducts = $basketProducts->map(function ($item) {
        $product = $item->product;
        return (object)[
            'name' => $product->name_en,
            'description' => $product->specification,
            'price' => $item->price,
            'image' => $product->main_image,
            'quantity' => $item->quantity,
            'total' => $item->quantity * $item->price,
        ];
    });

    // إنشاء الكتالوج في الداتابيز
    $catalog = Catalog::create([
        'name' => $request->name,
        'basket_id' => $basket->id,
        'template_id' => $template->id,
        'created_by' => $user->id,
    ]);

    // توليد الـ PDF باستخدام نفس View `templates.pdf`
    $pdf = Pdf::loadView('templates.pdf', [
        'template' => $template,
        'user' => $user,
        'client' => $template->client,
        'templateProducts' => $templateProducts,
    ])->setPaper('A4', 'portrait');

    // حفظ الملف
    $fileName = 'catalog_' . $catalog->id . '_' . time() . '.pdf';
    $filePath = 'catalogs/' . $fileName;
    Storage::disk('public')->put($filePath, $pdf->output());

    // تحديث مسار الملف داخل الكتالوج
    $catalog->update([
        'pdf_path' => $filePath,
    ]);

    // تحديث حالة الباسكت
    $basket->status = 'done';
    $basket->save();

    // تحميل العلاقات للرد
    $catalog->load('basket.basketProducts.product', 'template', 'creator');

    return response()->json([
        'message' => 'Basket converted to catalog successfully.',
        'data' => new CatalogResource($catalog),
        'pdf_url' => Storage::url($filePath),
    ], 201);
}

public function revertToBasket(Request $request, Catalog $catalog)
{
    // تحقق من الصلاحية
    if ($catalog->created_by !== Auth::id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // تحميل الباسكت
    $basket = $catalog->basket;
    if (!$basket) {
        return response()->json(['message' => 'No basket found for this catalog.'], 404);
    }

    // تغيير الحالة إلى pending
    $basket->status = 'in_progress';
    $basket->save();

    // (اختياري) حذف ملف الـ PDF
    if (Storage::disk('public')->exists($catalog->pdf_path)) {
        Storage::disk('public')->delete($catalog->pdf_path);
    }

    // (اختياري) حذف الكاتالوج نفسه
    $catalog->delete();

    return response()->json([
        'message' => 'Basket reverted. You can now edit the basket again.',
        'data' => $basket->load('basketProducts.product'),
    ]);
}


    // public function update(UpdateCatalogRequest $request, Catalog $catalog)
    // {
    //     $this->authorize('update', $catalog);
    //     $catalog = Catalog::find($catalog);
    //     if(!$catalog){
    //         return response()->json([
    //             'message' => 'Catalog not found.',
    //         ], 404);
    //     }
    //     $catalog->update($request->validated());
    //     $data =new CatalogResource($catalog);
    //     return response()->json([
    //         'message' => 'Catalog Updated Successfully',
    //         'data' => $data
    //     ],200);
    // }

    // public function destroy(Catalog $catalog)
    // {
    //     $this->authorize('delete', $catalog);
    //     $catalog = Catalog::find($catalog);
    //     if(!$catalog){
    //         return response()->json([
    //             'message' => 'Catalog not found.',
    //         ], 404);
    //     }
    //     $catalog->delete();
    //     return response()->json(['message' => 'Catalog Deleted Successfully'],200);
    // }
}
