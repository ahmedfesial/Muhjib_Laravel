<?php

namespace App\Http\Controllers;

use App\Models\ProductPrice;
use App\Http\Requests\StoreProductPriceRequest;
use App\Http\Requests\UpdateProductPriceRequest;
use App\Http\Resources\ProductPriceResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ProductPriceController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        // $this->authorize('viewAny', ProductPrice::class);
        $data=ProductPriceResource::collection(ProductPrice::all());
        return response()->json([
            'message' => 'Product Prices Retrieved Successfully',
            'data' => $data
        ],200);
    }

    public function store(StoreProductPriceRequest $request)
    {
        // $this->authorize('create', ProductPrice::class);
        $price = ProductPrice::create($request->validated());
        $data = new ProductPriceResource($price);
        return response()->json([
            'message' => 'Product Prices Created Successfully',
            'data' => $data
        ],201);
    }
    

    public function update(UpdateProductPriceRequest $request, ProductPrice $productPrice)
    {
        $this->authorize('update', $productPrice);
        $productPrice = ProductPrice::find($productPrice);
        if(!$productPrice){
            return response()->json([
                'message' => 'Product Price Not Found'
            ],404);
        }
        $productPrice->update($request->validated());
        $data = new ProductPriceResource($productPrice);
        return response()->json([
            'message' => 'Product Prices Updated Successfully',
            'data' => $data
        ],200);
    }

    public function destroy(ProductPrice $productPrice)
    {
        // $this->authorize('delete', $productPrice);
        // $productPrice = ProductPrice::find($productPrice);
        // if(!$productPrice){
        //     return response()->json([
        //         'message' => 'Product Price Not Found'
        //     ],404);
        // }
        $productPrice->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}

