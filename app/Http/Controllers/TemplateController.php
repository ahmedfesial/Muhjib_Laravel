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
            'cover_image_start' => 'nullable|image',
            'cover_image_end' => 'nullable|image',
        ]);
        $data['created_by'] = Auth::id();
        $template = Template::create($data);

        // رفع الصور
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('templates', 'public');
        }

        if ($request->hasFile('cover_image_start')) {
            $data['cover_image_start'] = $request->file('cover_image_start')->store('templates', 'public');
        }

        if ($request->hasFile('cover_image_end')) {
            $data['cover_image_end'] = $request->file('cover_image_end')->store('templates', 'public');
        }

        $template = Template::create($data);
        return response()->json(['message' => 'Template created', 'template_id' => $template->id]);
    }

    public function addClient(Request $request, Template $template)
    {
        $data = $request->validate([
            'client_name' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $template->client()->create($data);
        return response()->json(['message' => 'Client data saved']);
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
    $template->load(['client', 'templateProducts.product', 'creator']);

    $client = $template->client;
    $templateProducts = $template->templateProducts;
    $user = $template->creator; // مين أنشأ التمبليت

    $pdf = Pdf::loadView('templates.pdf', compact('template', 'user', 'client', 'templateProducts'))
              ->setPaper('A4', 'portrait');

    $fileName = 'template_' . $template->id . '_' . time() . '.pdf';
    $filePath = 'templates_pdfs/' . $fileName;

    Storage::disk('public')->put($filePath, $pdf->output());

    return response()->download(storage_path('app/public/' . $filePath));
}

}
