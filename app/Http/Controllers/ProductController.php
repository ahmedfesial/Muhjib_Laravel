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

class ProductController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        // $this->authorize('viewAny', Product::class);
        $query = Product::query();
        // Check if the request has a search parameter
        if ($request->has('search')) {
        $searchTerm = $request->input('search');
        $query->where(function($q) use ($searchTerm) {
            $q->where('name_en', 'like', "%$searchTerm%")
              ->orWhere('name_ar', 'like', "%$searchTerm%");
        });
    }
        // Check if the request has filter parameters
        if ($request->filled('name_en')) {
            $query->where('name_en', $request->name_en);
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        if ($request->filled('sub_category_id')) {
            $query->where('sub_category_id', $request->sub_category_id);
        }
        $products = $query->paginate(15);
        $data =ProductResource::collection($products);
        return response()->json([
            'message' => 'Products Retrieved Successfully',
            'data' => $data
        ],200);
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
    private function filter(Request $request){
        $query = Product::query();
        if ($request->filled('name_en')) {
            $query->where('name_en', $request->brand_id);
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('sub_category_id')) {
            $query->where('sub_category_id', $request->sub_category_id);
        }
    }
    // helper method inside class
    protected function uploadFile($request, $field, $folder)
{
    if ($request->hasFile($field)) {
        return $request->file($field)->store($folder, 'public');
    }
    return null;
}
    // 🔧 Helper: Delete file from storage
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
        // Prevent quantity from going below zero
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
        // $this->authorize('create', Product::class);

        $validated = $request->validate([
        'name_en' => 'nullable|string|max:255',
        'name_ar' => 'nullable|string|max:255',
        'features' => 'nullable|string',
        'main_color' => 'nullable|string|max:100',
        'brand_id' => 'nullable|exists:brands,id',
        'sub_category_id' => 'nullable|exists:sub_categories,id',
        'main_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'pdf_hs' => 'nullable|file|mimes:pdf|max:10000',
        'pdf_msds' => 'nullable|file|mimes:pdf|max:10000',
        'pdf_technical' => 'nullable|file|mimes:pdf|max:10000',
        'hs_code' => 'nullable|string|max:50',
        'sku' => 'nullable|string|max:100',
        'pack_size' => 'nullable|string|max:100',
        'dimensions' => 'nullable|string|max:100',
        'capacity' => 'nullable|string|max:100',
        'specification' => 'nullable|string',
        'price' => 'nullable|numeric|min:0',
        'is_visible' => 'boolean',
        'quantity' => 'required|integer|min:0',
        'certificates' => 'nullable|array',
        'certificates.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',

        'legends' => 'nullable|array',
        'legends.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
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
    $certificates = [];
if ($request->hasFile('certificates')) {
    foreach ($request->file('certificates') as $file) {
        $certificates[] = $file->store('products/certificates', 'public');
    }
}

$legends = [];
if ($request->hasFile('legends')) {
    foreach ($request->file('legends') as $file) {
        $legends[] = $file->store('products/legends', 'public');
    }
}

// ثم أضفهم إلى البيانات قبل الإنشاء
$validated['certificates'] = $certificates;
$validated['legends'] = $legends;

    $product = Product::create($validated);
        $data = new ProductResource($product);
        return response()->json([
            'message' =>'Product Created Successfully',
            'data' => $data
        ],201);
    }

    public function show($id)
    {
        // $this->authorize('view', $product);
        $product = Product::find($id);
        if(!$product){
            return response()->json([
                'message' => 'Product Not found'
            ],404);
        }
        $data =new ProductResource($product);
        return response()->json([
            'message' => 'Product Retrieved Successfully',
            'data' => $data
        ],200);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        // $this->authorize('update', $product);
        // $product = Product::find($product);
        // if(!$product){
        //     return response()->json([
        //         'message' => 'Product Not found'
        //     ],404);
        // }
        $data = $request->validated();
         // Optional: delete old files if new ones uploaded
        if ($request->hasFile('main_image')) {
        $this->deleteFile($product->main_image);
        $data['main_image'] = $this->uploadFile($request, 'main_image', 'products/images');
    }

    foreach (['pdf_hs', 'pdf_msds', 'pdf_technical'] as $pdfField) {
        if ($request->hasFile($pdfField)) {
            $this->deleteFile($product->$pdfField);
            $data[$pdfField] = $this->uploadFile($request, $pdfField, 'products/pdfs');
        }
    }
    if ($request->hasFile('certificates')) {
    // حذف الصور القديمة
    foreach ($product->certificates ?? [] as $old) {
        $this->deleteFile($old);
    }
    $certificates = [];
    foreach ($request->file('certificates') as $file) {
        $certificates[] = $file->store('products/certificates', 'public');
    }
    $data['certificates'] = $certificates;
}

if ($request->hasFile('legends')) {
    foreach ($product->legends ?? [] as $old) {
        $this->deleteFile($old);
    }
    $legends = [];
    foreach ($request->file('legends') as $file) {
        $legends[] = $file->store('products/legends', 'public');
    }
    $data['legends'] = $legends;
}
        $product->update($data);
        $updatedData=new ProductResource($product);
        return response()->json([
            'message' => 'Product Updated Successfully',
            'data' => $updatedData
        ],200);
    }


    public function destroy(Product $product)
    {
        // $this->authorize('delete', $product);
        // $product = Product::find($product);
        // if(!$product){
        //     return response()->json([
        //         'message' => 'Product Not found'
        //     ],404);
        // }
        // Delete associated files
        $this->deleteFile($product->main_image);
        $this->deleteFile($product->pdf_hs);
        $this->deleteFile($product->pdf_msds);
        $this->deleteFile($product->pdf_technical);
        foreach ($product->certificates ?? [] as $file) {
    $this->deleteFile($file);
}

foreach ($product->legends ?? [] as $file) {
    $this->deleteFile($file);
}
        $product->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
