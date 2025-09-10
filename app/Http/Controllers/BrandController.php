<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BrandController extends Controller
{

    public function index(Request $request)
    {
        // $this->authorize('viewAny', Brand::class);
        $query = $this->filter($request);
    $brands = $query->paginate(10);

    $data = BrandResource::collection($brands);

    return response()->json([
        'message' => 'Brands Retrieved Successfully',
        'data' => $data
    ], 200);
    }

   private function filter(Request $request)
{
    $query = Brand::query();

    // ✅ رجّع البراندات المفعلة فقط
    $query->where('status', true);

    // لو حابب تحافظ على فلترة is_hidden كمان، سيب السطر ده:
    // $query->where('is_hidden', false);

    if ($request->filled('name_en')) {
        $query->where('name_en', 'like', '%' . $request->name_en . '%');
    }

    if ($request->filled('name_ar')) {
        $query->where('name_ar', 'like', '%' . $request->name_ar . '%');
    }

    if ($request->filled('color_code')) {
        $query->where('color_code', $request->color_code);
    }

    return $query;
}


public function toggleStatus($id)
{
    $brand = Brand::find($id);

    if (!$brand) {
        return response()->json(['message' => 'Brand not found'], 404);
    }

    $brand->status = !$brand->status; // تبديل الحالة
    $brand->save();

    return response()->json([
        'message' => $brand->status ? 'Brand activated' : 'Brand deactivated',
        'status'  => $brand->status
    ], 200);
}





public function store(Request $request)
{
    $validated = $request->validate([
        'name_en' => 'required|string|max:255',
        'name_ar' => 'required|string|max:255',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        'short_description_en' => 'nullable|string',
        'short_description_ar' => 'nullable|string',
        'full_description_en' => 'nullable|string',
        'full_description_ar' => 'nullable|string',
        'background_image_url' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:4096',
        'color_code' => 'nullable|string|max:7',
        'catalog_pdf_url' => 'nullable|mimes:pdf|max:10000',
        'status' => 'nullable|boolean',
    ]);

    if ($request->hasFile('logo')) {
        $validated['logo'] = $request->file('logo')->store('brands/logos', 'public');
    }

    if ($request->hasFile('background_image_url')) {
        $validated['background_image_url'] = $request->file('background_image_url')->store('brands/backgrounds', 'public');
    }

    if ($request->hasFile('catalog_pdf_url')) {
        $validated['catalog_pdf_url'] = $request->file('catalog_pdf_url')->store('brands/catalogs', 'public');
    }

    $brand = Brand::create($validated);

    return response()->json([
        'message' => 'Brand created successfully',
        'data' => new BrandResource($brand)
    ], 201);
}



       public function show($id){
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(['message' => 'Brand not found',], 404);
        }

        $data = new BrandResource($brand);
        return response()->json(['message' => 'Brand fetched successfully','data' => $data,], 200);
    }

    public function update(Request $request, Brand $brand)
{
    $validated = $request->validate([
        'name_en' => 'sometimes|required|string|max:255',
        'name_ar' => 'sometimes|required|string|max:255',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        'short_description_en' => 'nullable|string',
        'short_description_ar' => 'nullable|string',
        'full_description_en' => 'nullable|string',
        'full_description_ar' => 'nullable|string',
        'background_image_url' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:4096',
        'color_code' => 'nullable|string|max:7',
        'catalog_pdf_url' => 'nullable|mimes:pdf|max:10000',
        'status' => 'nullable|boolean',
    ]);

    if ($request->hasFile('logo')) {
        $validated['logo'] = $request->file('logo')->store('brands/logos', 'public');
    }

    if ($request->hasFile('background_image_url')) {
        $validated['background_image_url'] = $request->file('background_image_url')->store('brands/backgrounds', 'public');
    }

    if ($request->hasFile('catalog_pdf_url')) {
        $validated['catalog_pdf_url'] = $request->file('catalog_pdf_url')->store('brands/catalogs', 'public');
    }
    // dd($validated); // array of validated data
    $brand->update($validated);
    $data = new BrandResource($brand);

    return response()->json([
        'message' => 'Brand updated successfully',
        'data' => $data
    ], 200);
}




    public function destroy(Brand $brand)
    {
        // $this->authorize('delete', $brand);

        $brand->delete();
        return response()->json(['message' => 'Brand deleted successfully'],200);
    }
}
