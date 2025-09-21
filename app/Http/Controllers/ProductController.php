<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Mpdf\Mpdf;

class ProductController extends Controller
{
    // use AuthorizesRequests;
    public function index(Request $request)
{
    $query = Product::with(['certificates', 'legends', 'prices']);
    // 🔍 بحث عام (search)
    if ($request->has('search')) {
        $searchTerm = $request->input('search');
        $query->where(function($q) use ($searchTerm) {
            $q->where('name_en', 'like', "%$searchTerm%")
              ->orWhere('name_ar', 'like', "%$searchTerm%")
              ->orWhere('sku', 'like', "%$searchTerm%"); // بحث بالـ SKU في البحث العامنضيف ال
        });
    }

    if ($request->filled('name_en')) {
        $query->where('name_en', $request->name_en);
    }

    if ($request->filled('brand_id')) {
        $query->where('brand_id', $request->brand_id);
    }

    if ($request->filled('sub_category_id')) {
        $query->where('sub_category_id', $request->sub_category_id);
    }

    if ($request->filled('sku')) {
        $query->where('sku', 'like', '%' . $request->sku . '%'); // فلترة بالـ SKU
    }

    $products = $query->paginate(15);
    return response()->json([
        'message' => 'Products Retrieved Successfully',
        'data' => $products,
    ], 200);
}

    private function search(Request $request){
        $query = Product::query();
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'LIKE', "%{$search}%")
                  ->orWhere('name_ar', 'LIKE', "%{$search}%");
            });
        }
    }
    private function filter(Request $request)
{
    $query = Product::query();

    if ($request->filled('name_en')) {
        $query->where('name_en', $request->name_en);
    }

    if ($request->filled('brand_id')) {
        $query->where('brand_id', $request->brand_id);
    }

    if ($request->filled('sub_category_id')) {
        $query->where('sub_category_id', $request->sub_category_id);
    }

    if ($request->filled('sku')) {
        $query->where('sku', 'like', '%' . $request->sku . '%');
    }

    return $query;
}

    protected function uploadFile($request, $field, $folder)
{
    if ($request->hasFile($field)) {
        return $request->file($field)->store($folder, 'public');
    }
    return null;
}
    protected function deleteFile($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
    // Update Quantity Function
    public function updateQuantity(Request $request, Product $product)
{
    // Validate input
    $request->validate([
        'action' => 'required|in:increase,decrease',
        'amount' => 'required|integer|min:1',
    ]);

    $action = $request->input('action');
    $amount = $request->input('amount');

    if ($action === 'increase') {
        $product->quantity += $amount;
    } elseif ($action === 'decrease') {
        $product->quantity = max(0, $product->quantity - $amount);
    }

    $product->save();

    return response()->json([
        'message' => 'Quantity updated successfully',
        'quantity' => $product->quantity,
    ]);
}

    public function store(StoreProductRequest $request)
    {

        $validated = $request->validate([
        'name_en' => 'nullable|string|max:255',
        'name_ar' => 'nullable|string|max:255',
        'description_ar' => 'nullable|string',
        'features' => 'nullable|string',
        'main_colors' => 'nullable|array',
        'main_colors.*' => 'nullable',
        'brand_id' => 'nullable|exists:brands,id',
        'sub_category_id' => 'nullable|exists:sub_categories,id',
        'main_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'pdf_hs' => 'nullable|file|mimes:pdf|max:10000',
        'pdf_msds' => 'nullable|file|mimes:pdf|max:10000',
        'pdf_technical' => 'nullable|file|mimes:pdf|max:10000',
        'hs_code' => 'nullable|string|max:50',
        'sku' => 'nullable|string|max:100|unique:products,sku',
        'pack_size' => 'nullable|string|max:100',
        'dimensions' => 'nullable|string|max:100',
        'capacity' => 'nullable|string|max:100',
        'specification' => 'nullable|string',
        'price' => 'nullable|numeric|min:0',
        'is_visible' => 'boolean',
        'quantity' => 'required|integer|min:0',
        'certificate_ids' => 'nullable|array',
        'certificate_ids.*' => 'exists:certificates,id',
        'legend_ids' => 'nullable|array',
        'legend_ids.*' => 'exists:legends,id',
        'images' => 'nullable|array',
        'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'prices' => 'nullable|array',
        'prices.*.price_type' => 'required|string',
        'prices.*.value' => 'required|numeric',
    ]);

    if ($request->hasFile('main_image')) {
        $validated['main_image'] = $request->file('main_image')->store('products/images', 'public');
    }

    if ($request->hasFile('pdf_hs')) {
        $validated['pdf_hs'] = $request->file('pdf_hs')->store('products/pdfs', 'public');
    }

    if ($request->hasFile('pdf_msds')) {
        $validated['pdf_msds'] = $request->file('pdf_msds')->store('products/pdfs', 'public');
    }

    if ($request->hasFile('pdf_technical')) {
        $validated['pdf_technical'] = $request->file('pdf_technical')->store('products/pdfs', 'public');
    }
    if (!empty($validated['sku']) && Product::where('sku', $validated['sku'])->exists()) {
        return response()->json(['message' => 'SKU already exists'], 422);
    }

    $images = [];
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $images[] = $image->store('products/images', 'public');
        }
    }
    $validated['images'] = $images;

    $certificates = [];


$legends = [];
$mainColors = [];

if ($request->has('main_colors')) {
    foreach ($request->file('main_colors', []) ?? [] as $index => $colorFile) {
        if ($colorFile instanceof \Illuminate\Http\UploadedFile) {
            $mainColors[] = $colorFile->store('products/colors', 'public');
        }
    }

    foreach ($request->input('main_colors', []) as $index => $colorText) {
        if (!empty($colorText) && !($colorText instanceof \Illuminate\Http\UploadedFile)) {
            $mainColors[] = $colorText;
        }
    }
}

$validated['main_colors'] = $mainColors;
  $pricesData = $validated['prices'];

$product = Product::create($validated);

foreach ($pricesData as $price) {
    $product->prices()->create($price);
}


    $product->load('prices');
if ($request->has('certificate_ids')) {
    $product->certificates()->sync($request->certificate_ids);
}

if ($request->has('legend_ids')) {
    $product->legends()->sync($request->legend_ids);
}

    $product->load(['certificates', 'legends']);
        return response()->json([
            'message' =>'Product Created Successfully',
            'data' => new ProductResource($product),
        ],201);
    }

    public function show($id)
    {
        $product = Product::with(['certificates', 'legends', 'prices'])->find($id);
        if(!$product){
            return response()->json([
                'message' => 'Product Not found'
            ],404);
        }
        return response()->json([
            'message' => 'Product Retrieved Successfully',
            'data' => $product,
        ],200);
    }
private function isImagePath($value)
{
    return is_string($value) && Str::endsWith($value, ['.jpg', '.jpeg', '.png', '.webp']);
}

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
        'name_en' => 'nullable|string|max:255',
        'name_ar' => 'nullable|string|max:255',
        'description_ar' => 'nullable|string',
        'features' => 'nullable|string',
        'main_colors' => 'nullable|array',
        'main_colors.*' => 'nullable',
        'brand_id' => 'nullable|exists:brands,id',
        'sub_category_id' => 'nullable|exists:sub_categories,id',
        'main_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'pdf_hs' => 'nullable|file|mimes:pdf|max:10000',
        'pdf_msds' => 'nullable|file|mimes:pdf|max:10000',
        'pdf_technical' => 'nullable|file|mimes:pdf|max:10000',
        'hs_code' => 'nullable|string|max:50',
        'sku' => 'nullable|string|max:100|unique:products,sku',
        'pack_size' => 'nullable|string|max:100',
        'dimensions' => 'nullable|string|max:100',
        'capacity' => 'nullable|string|max:100',
        'specification' => 'nullable|string',
        'price' => 'nullable|numeric|min:0',
        'is_visible' => 'boolean',
        'quantity' => 'nullable|integer|min:0',
        'certificate_ids' => 'nullable|array',
        'certificate_ids.*' => 'exists:certificates,id',
        'legend_ids' => 'nullable|array',
        'legend_ids.*' => 'exists:legends,id',
        'images' => 'nullable|array',
        'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'prices' => 'nullable|array',
        'prices.*.price_type' => 'nullable|string',
        'prices.*.value' => 'nullable|numeric',
    ]);
        if ($request->hasFile('main_image')) {
        $this->deleteFile($product->main_image);
        $data['main_image'] = $this->uploadFile($request, 'main_image', 'products/images');
    }
   if ($request->has('images')) {
        // حذف الصور القديمة من التخزين
        foreach ($product->images ?? [] as $oldImage) {
            $this->deleteFile($oldImage);
        }

        $newImages = [];

        foreach ($request->file('images') as $imageFile) {
            $newImages[] = $imageFile->store('products/images', 'public');
        }

        $data['images'] = $newImages;
    }

    if (!empty($data['sku']) && Product::where('sku', $data['sku'])->where('id', '!=', $product->id)->exists()) {
        return response()->json(['message' => 'SKU already exists'], 422);
    }



    $colors = [];

if ($request->has('main_colors')) {
    foreach ($product->main_colors ?? [] as $oldColor) {
        if ($this->isImagePath($oldColor)) {
            $this->deleteFile($oldColor);
        }
    }

    foreach ($request->main_colors as $color) {
        if ($color instanceof \Illuminate\Http\UploadedFile) {
            $colors[] = $color->store('products/colors', 'public');
        } else {
            $colors[] = $color;
        }
    }

    $data['main_colors'] = $colors;
}


    foreach (['pdf_hs', 'pdf_msds', 'pdf_technical'] as $pdfField) {
        if ($request->hasFile($pdfField)) {
            $this->deleteFile($product->$pdfField);
            $data[$pdfField] = $this->uploadFile($request, $pdfField, 'products/pdfs');
        }
    }

    $certificates = [];
    $legends = [];
        $product->update($data);
if ($request->has('certificate_ids')) {
    $product->certificates()->sync($request->certificate_ids);
}

if ($request->has('legend_ids')) {
    $product->legends()->sync($request->legend_ids);
}
    $product = $product->fresh();
// dd($product->images);
        // $updatedData=new ProductResource($product);
        return response()->json([
        'message' => 'Product Updated Successfully',
        'data' => new ProductResource($product),
    ], 200);

    }


public function downloadTechnicalSheet(Product $product)
{
    $product->load(['certificates', 'legends', 'brand', 'subCategory']);

    $productUrl = url('/products/' . $product->id);

    // ✅ هنا بنحدد إن الفورمات PNG عشان يستخدم GD مش Imagick
    $qrCode = base64_encode(
        QrCode::format('svg')
            ->size(200)
            ->errorCorrection('H')
            ->generate($productUrl)
    );

    $pdf = Pdf::loadView('pdf.technical_sheet', [
        'product' => $product,
        'qrCode'  => $qrCode,
    ]);

    return $pdf->download("Technical_Data_Sheet_{$product->id}.pdf");
}


    public function destroy(Product $product)
    {
        $this->deleteFile($product->main_image);
        $this->deleteFile($product->pdf_hs);
        $this->deleteFile($product->pdf_msds);
        $this->deleteFile($product->pdf_technical);
        $product->certificates()->detach();
        $product->legends()->detach();

        $product->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
