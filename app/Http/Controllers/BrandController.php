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

    public function index()
    {
        // $this->authorize('viewAny', Brand::class);
        $brand = Brand::paginate(10);
        $data = BrandResource::collection($brand);
        return response()->json(['message'=>'Brands Retrieved Successfully', 'data' => $data],200);
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

    public function show(Brand $brand)
    {
        // $this->authorize('view', $brand);
        $data =new BrandResource($brand);
        return response()->json(['message'=>'Brand Retrieved Successfully', 'data' => $data],200);
    }

    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        // $this->authorize('update', $brand);
        $brand = Brand::find($brand);
        if(!$brand){
            return response()->json([
                'message' => 'Brand not found.',
            ], 404);
        }
        $brand->update($request->validated());
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('brands/logos', 'public');
        }

        if ($request->hasFile('background_image_url')) {
            $data['background_image_url'] = $request->file('background_image_url')->store('brands/backgrounds', 'public');
        }

        if ($request->hasFile('catalog_pdf_url')) {
            $data['catalog_pdf_url'] = $request->file('catalog_pdf_url')->store('brands/catalogs', 'public');
        }
        $data =new BrandResource($brand);
        return response()->json(['message'=>'Brand Updated Successfully', 'data' => $data],200);
    }

    public function destroy(Brand $brand)
    {
        // $this->authorize('delete', $brand);

        $brand->delete();
        return response()->json(['message' => 'Brand deleted successfully'],200);
    }
}
