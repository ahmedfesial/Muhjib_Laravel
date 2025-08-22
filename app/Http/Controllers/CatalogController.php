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
    public function index()
    {
        // $this->authorize('viewAny', Catalog::class);
        $catalogs = Catalog::with(['basket', 'template'])->latest()->get();
        $data =CatalogResource::collection($catalogs);
        return response()->json([
            'message' => 'Catalogs Retrieved Successfully',
            'data' =>$data
        ],200);
    }

    public function store(StoreCatalogRequest $request)
{
    // $this->authorize('create', Catalog::class);

    $basket = Basket::with(['products'])->findOrFail($request->basket_id);
    $template = Template::findOrFail($request->template_id);

    // PDF Generation Logic
    if (!view()->exists('pdf.templates.custom_template')) {
        return response()->json(['error' => 'PDF view not found'], 404);
    }

    $pdf = PDF::loadView('pdf.templates.custom_template', [
        'basket' => $basket,
        'template' => $template,
    ]);

    $pdfPath = 'catalogs/' . Str::uuid() . '.pdf';
    Storage::put("public/$pdfPath", $pdf->output());

    $catalog = Catalog::create([
        'name' => $request->name,
        'basket_id' => $request->basket_id,
        'template_id' => $request->template_id,
        'created_by' => Auth::id(),
        'pdf_path' => $pdfPath,
    ]);

    $data = new CatalogResource($catalog);

    return response()->json([
        'message' => 'Catalog Created Successfully',
        'data' => $data
    ], 201);
}
    public function show(Catalog $catalog)
    {
        // $this->authorize('view', $id);
        // $catalog= Catalog::find($id);
        // if(!$catalog){
        //     return response()->json([
        //         'message' => 'Catalog not found.',
        //     ], 404);
        // }
        $data=new CatalogResource($catalog);
        return response()->json([
            'message' => 'Catalog Retrieved Successfully',
            'data' => $data
        ],200);
    }

   public function generateCatalog(Request $request)
{
    $request->validate([
        'template_id' => 'required|exists:templates,id',
        'products' => 'required|array',
        'products.*' => 'exists:products,id',
    ]);

    $template = Template::findOrFail($request->template_id);
    $user = Auth::user();
    $products = Product::whereIn('id', $request->products)->get();

    $catalog = Catalog::create([
        'user_id' => $user->id,
        'template_id' => $template->id,
        'basket_id' => $request->basket_id,
        'title' => 'My Custom Catalog',
    ]);

    // Start HTML
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            body { font-family: DejaVu Sans, sans-serif; margin: 40px; }
            h1, h2, h3, p { text-align: center; }
            .page-break { page-break-after: always; }
            .product { margin: 30px 0; text-align: center; }
            .product img { max-width: 200px; max-height: 200px; margin-bottom: 10px; }
            .product-name { font-size: 20px; font-weight: bold; margin-bottom: 5px; }
            .product-description { margin-bottom: 5px; font-size: 14px; }
            .product-price { font-size: 16px; font-weight: bold; color: #333; }
        </style>
    </head>
    <body>

        <!-- غلاف أمامي -->
        <div>
            <h1>' . $catalog->title . '</h1>
            <h3>Prepared for: ' . $user->name . '</h3>
            <p>Date: ' . now()->format('Y-m-d') . '</p>
        </div>

        <div class="page-break"></div>
    ';

    // المنتجات
    foreach ($products as $index => $product) {
        $main_image = $product->main_image ? Storage::url($product->main_image) : 'https://via.placeholder.com/200';
        $html .= '
        <div class="product">
            <img src="' . $main_image . '" alt="Product Image">
            <div class="product-name">' . $product->name_en . '</div>
            <div class="product-specification">' . ($product->specification ?? 'No description available') . '</div>
            <div class="product-price">Price: ' . number_format($product->price, 2) . ' EGP</div>
        </div>';


    }

    // غلاف خلفي
    $html .= '
        <div class="page-break"></div>
        <div>
            <h1>Thank You!</h1>
            <p>We hope you enjoyed browsing our catalog.</p>
            <p>Contact us at: superadmin@gmail.com</p>
        </div>

    </body>
    </html>';

    $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');

    $filename = 'catalog_' . time() . '.pdf';
    Storage::disk('public')->put($filename, $pdf->output());

    $url = Storage::url($filename);

    return response()->json([
        'message' => 'Catalog saved successfully.',
        'file_url' => $url,
    ]);
}

public function convertToCatalog(Request $request, Basket $basket)
{
    // ✅ Validate input
    $request->validate([
        'template_id' => 'required|exists:templates,id',
        'name' => 'required|string|max:255',
    ]);

    // ✅ Check if basket already converted
    if ($basket->status === 'converted') {
        return response()->json([
            'message' => 'This basket has already been converted to a catalog.',
        ], 400);
    }

    $template = Template::findOrFail($request->template_id);

    // ✅ Load products from basketProducts with related product
    $basketProducts = $basket->basketProducts()->with('product')->get();

    // ✅ Start generating the HTML for PDF
    $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                body { font-family: DejaVu Sans, sans-serif; margin: 40px; }
                h1, h2, h3, p { text-align: center; }
                .product { margin-bottom: 40px; page-break-inside: avoid; }
                .product img { max-width: 150px; max-height: 150px; display: block; margin: 10px auto; }
                .product-name { font-size: 18px; font-weight: bold; text-align: center; }
                .product-description { text-align: center; font-size: 14px; margin-bottom: 10px; }
                .product-info { text-align: center; font-size: 14px; }
                .page-break { page-break-after: always; }
            </style>
        </head>
        <body>
            <h1>' . $request->name . '</h1>
            <h3>Created for: ' . (Auth::user()->name ?? 'User') . '</h3>
            <p>Date: ' . now()->format('Y-m-d') . '</p>
            <div class="page-break"></div>
    ';

    foreach ($basketProducts as $item) {
        $product = $item->product;
        if (!$product) continue;

        $imageUrl = $product->main_image
            ? asset('storage/' . $product->main_image)
            : 'https://via.placeholder.com/150';

        $html .= '
            <div class="product">
                <img src="' . $imageUrl . '" alt="Product Image" />
                <div class="product-name">' . $product->name_en . '</div>
                <div class="product-description">' . ($product->specification ?? 'No description') . '</div>
                <div class="product-info">Quantity: ' . $item->quantity . '</div>
                <div class="product-info">Unit Price: ' . number_format($item->price, 2) . ' EGP</div>
                <div class="product-info">Total: ' . number_format($item->quantity * $item->price, 2) . ' EGP</div>
            </div>
        ';
    }

    $html .= '
            <div class="page-break"></div>
            <div style="text-align: center;">
                <h2>Thank You!</h2>
                <p>We hope you enjoyed browsing our catalog.</p>
                <p>Contact us at: support@example.com</p>
            </div>
        </body>
        </html>
    ';

    // ✅ Generate the PDF
    $pdf = PDF::loadHTML($html)->setPaper('A4', 'portrait');
    $pdfPath = 'catalogs/' . Str::uuid() . '.pdf';
    Storage::disk('public')->put($pdfPath, $pdf->output());

    // ✅ Create the catalog
    $catalog = Catalog::create([
        'name' => $request->name,
        'basket_id' => $basket->id,
        'template_id' => $template->id,
        'created_by' => Auth::id(),
        'pdf_path' => $pdfPath,
    ]);

    // ✅ Update basket status
    $basket->status = 'done';
    $basket->save();

    // ✅ Load relationships for API response
    $catalog->load('basket.basketProducts.product', 'template', 'creator');

    return response()->json([
        'message' => 'Basket converted to catalog successfully.',
        'data' => new CatalogResource($catalog),
    ], 201);
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
