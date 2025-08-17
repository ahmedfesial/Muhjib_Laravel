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
<<<<<<< HEAD
use Illuminate\Http\JsonResponse;
=======
use Illuminate\Http\JsonResponse;   

>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e

class ProductController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
<<<<<<< HEAD
        $this->authorize('viewAny', Product::class);
=======
        // $this->authorize('viewAny', Product::class);
>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e
        $query = Product::query();
        // Check if the request has a search parameter
        if (request()->has('search')) {
            $query = $this->search(request());
        }
        // Check if the request has filter parameters
        if (request()->has('brand_id') || request()->has('sub_category_id')) {
            $query = $this->filter(request());
        }
        $products = $query->latest()->paginate(15);
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
<<<<<<< HEAD
        $query = Product::query();
=======
        $query = Product::query();     
>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('sub_category_id')) {
            $query->where('sub_category_id', $request->sub_category_id);
        }
    }
    // helper method inside class
    protected function uploadFile($request, $field, $folder)
<<<<<<< HEAD
{
    if ($request->hasFile($field)) {
        return $request->file($field)->store($folder, 'public');
    }
    return null;
}
=======
    {
        if ($request->hasFile($field)) {
            return $request->file($field)->store($folder, 'public');
        }
        return null;
    }
>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e
    // 🔧 Helper: Delete file from storage
    protected function deleteFile($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
<<<<<<< HEAD
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
=======
>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e

    public function store(StoreProductRequest $request)
    {
        // $this->authorize('create', Product::class);

        $data = $request->validated();

        // Handle image and PDF uploads
        $data['main_image'] = $this->uploadFile($request, 'main_image', 'products/images');
        $data['pdf_hs'] = $this->uploadFile($request, 'pdf_hs', 'products/pdfs');
        $data['pdf_msds'] = $this->uploadFile($request, 'pdf_msds', 'products/pdfs');
        $data['pdf_technical'] = $this->uploadFile($request, 'pdf_technical', 'products/pdfs');

        $product = Product::create($data);
        $data = new ProductResource($product);
        return response()->json([
            'message' =>'Product Created Successfully',
            'data' => $data
        ],201);
    }

<<<<<<< HEAD
    public function show($id)
    {
        // $this->authorize('view', $product);
        $product = Product::find($id);
=======
    public function show(Product $product)
    {
        $this->authorize('view', $product);
        $product = Product::find($product);
>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e
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
        $this->authorize('update', $product);
        $product = Product::find($product);
        if(!$product){
            return response()->json([
                'message' => 'Product Not found'
            ],404);
        }
        $data = $request->validated();
         // Optional: delete old files if new ones uploaded
        if ($request->hasFile('main_image')) {
<<<<<<< HEAD
        $this->deleteFile($product->main_image);
        $data['main_image'] = $this->uploadFile($request, 'main_image', 'products/images');
    }

    foreach (['pdf_hs', 'pdf_msds', 'pdf_technical'] as $pdfField) {
        if ($request->hasFile($pdfField)) {
            $this->deleteFile($product->$pdfField);
            $data[$pdfField] = $this->uploadFile($request, $pdfField, 'products/pdfs');
        }
    }
=======
            $this->deleteFile($product->main_image);
            $data['main_image'] = $this->uploadFile($request, 'main_image', 'products/images');
        }

        foreach (['pdf_hs', 'pdf_msds', 'pdf_technical'] as $pdfField) {
            if ($request->hasFile($pdfField)) {
                $this->deleteFile($product->$pdfField);
                $data[$pdfField] = $this->uploadFile($request, $pdfField, 'products/pdfs');
            }
        }
>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e
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
        $product->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
