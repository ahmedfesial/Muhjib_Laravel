<?php
namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\TemplateClient;
use App\Models\TemplateProduct;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TemplateController extends Controller
{
   public function store(Request $request)
{
    $data = $request->validate([
        'name' => 'required|string',
        'description' => 'nullable|string',
        'logo' => 'nullable|image',
        'cover_image_start.*' => 'nullable|image',
        'cover_image_end.*' => 'nullable|image',
    ]);

    $data['created_by'] = Auth::id();

    if ($request->hasFile('logo')) {
        $data['logo'] = $request->file('logo')->store('templates', 'public');
    }

    $template = Template::create($data);

    // رفع صور البداية
    if ($request->hasFile('cover_image_start')) {
        foreach ($request->file('cover_image_start') as $image) {
            $path = $image->store('templates', 'public');
            $template->coverImages()->create([
                'path' => $path,
                'position' => 'start',
            ]);
        }
    }

    // رفع صور النهاية
    if ($request->hasFile('cover_image_end')) {
        foreach ($request->file('cover_image_end') as $image) {
            $path = $image->store('templates', 'public');
            $template->coverImages()->create([
                'path' => $path,
                'position' => 'end',
            ]);
        }
    }

    return response()->json(['message' => 'Template created', 'template_id' => $template->id]);
}


    public function addClient(Request $request, Template $template)
{
    $data = $request->validate([
        'client_id' => 'required|exists:clients,id',
    ]);

    // لو العميل مضاف بالفعل لنفس التمبليت
    if ($template->client()->exists()) {
        return response()->json(['message' => 'Template already has a client'], 400);
    }

    $client = \App\Models\Client::find($data['client_id']);

    $template->client()->create([
        'client_id' => $client->id,
        'client_name' => $client->name,
        'email' => $client->email,
        'phone' => $client->phone,
        'address' => $client->address,
        'logo' => $client->logo,
    ]);

    return response()->json(['message' => 'Client linked and data saved']);
}



    public function addProductToTemplate(Request $request, Template $template)
{
    $products = $request->input('products');

    if (!empty($products)) {
        foreach ($products as $productId) {
            $product = Product::find($productId);
            if ($product) {
                $template->templateProducts()->create([
                    'product_id' => $product->id,
                    'template_id' => $template->id,
                    'name' => $product->name_en ?? 'No Name Provided',
                    'description' => $product->description ?? null,
                    'price' => $product->price ?? 0,
                    'image' => $product->main_image ?? null,
                ]);
            }
        }
    }

    return response()->json(['message' => 'Products added to template']);
}
public function generatePDF(Template $template)
{
    $template->load([
        'client',
        'templateProducts.product.subCategory',
        'creator',
        'startCoverImages',
        'endCoverImages',
    ]);

    $client = $template->client;
    $user = $template->creator;

    // تأكد تحميل العلاقات
    $template->templateProducts->load('product.subCategory');

    // جروب المنتجات حسب sub category
    $groupedProducts = $template->templateProducts->groupBy(function ($item) {
        return optional($item->product->subCategory)->id;
    });

    $pdf = Pdf::loadView('templates.pdf', compact('template', 'user', 'client', 'groupedProducts'))
              ->setPaper('A4', 'portrait');

    $fileName = 'template_' . $template->id . '_' . time() . '.pdf';
    $filePath = 'templates_pdfs/' . $fileName;

    Storage::disk('public')->put($filePath, $pdf->output());

    return response()->download(storage_path('app/public/' . $filePath));
}



public function uploadCoverImages(Request $request, Template $template)
{
    $request->validate([
        'images.*' => 'required|image',
        'position' => 'required|in:start,end',
        'background_position' => 'nullable|in:client,products',
    ]);

    foreach ($request->file('images') as $image) {
        $path = $image->store('templates/covers', 'public');

        $template->coverImages()->create([
            'path' => $path,
            'position' => $request->position,
            'background_position' => $request->background_position, // NEW
        ]);
    }

    return response()->json(['message' => 'Images uploaded successfully.']);
}

}
