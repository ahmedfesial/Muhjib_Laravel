<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BrandController extends Controller
{
    use AuthorizesRequests;
    // public function __construct()
    // {
    //     parent::__construct();
    //     $this->authorizeResource(Brand::class, 'brand');
    // }

    public function index(Request $request)
    {
        // $this->authorize('viewAny', Brand::class);
        $query = $this->filter($request);

    // Paginate results
    $brands = $query->paginate(10);

    // Transform using BrandResource
    $data = BrandResource::collection($brands);

    return response()->json([
        'message' => 'Brands Retrieved Successfully',
        'data' => $data
    ], 200);
    }

    private function filter(Request $request)
{
    $query = Brand::query();

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

    public function store(StoreBrandRequest $request)
    {
        // $this->authorize('create', Brand::class);
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('brands/logos', 'public');
        }

        if ($request->hasFile('background_image_url')) {
            $data['background_image_url'] = $request->file('background_image_url')->store('brands/backgrounds', 'public');
        }

        if ($request->hasFile('catalog_pdf_url')) {
            $data['catalog_pdf_url'] = $request->file('catalog_pdf_url')->store('brands/catalogs', 'public');
        }
        $brand = Brand::create($data);
        $data =new BrandResource($brand);
        return response()->json(['message'=>'Brand Created Successfully', 'data' => $data],201);
    }


       public function show($id){
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(['message' => 'Brand not found',], 404);
        }

        $data = new BrandResource($brand);
        return response()->json(['message' => 'Brand fetched successfully','data' => $data,], 200);
    }

    public function update(UpdateBrandRequest $request,string $id){
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(['message' => 'Brand not found',], 404);
        }

        $validatedData = $request->validated();
        $brand->update($validatedData);
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('brands/logos', 'public');
        }

        if ($request->hasFile('background_image_url')) {
            $data['background_image_url'] = $request->file('background_image_url')->store('brands/backgrounds', 'public');
        }

        if ($request->hasFile('catalog_pdf_url')) {
            $data['catalog_pdf_url'] = $request->file('catalog_pdf_url')->store('brands/catalogs', 'public');
        }
        $data = new BrandResource($brand);

        return response()->json(['message' => 'Brand updated successfully','data' => $data,], 200);
    }


    public function destroy(Brand $brand)
    {
        // $this->authorize('delete', $brand);

        $brand->delete();
        return response()->json(['message' => 'Brand deleted successfully'],200);
    }
}
