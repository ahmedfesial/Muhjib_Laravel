<?php

namespace App\Http\Controllers;

use App\Models\ProductPrice;
use App\Http\Requests\StoreProductPriceRequest;
use App\Http\Requests\UpdateProductPriceRequest;
use App\Http\Resources\ProductPriceResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Exports\ProductPriceExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductPriceImport;
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
 public function import(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:xlsx,xls',
    ]);

    $import = new ProductPriceImport();
    Excel::import($import, $request->file('file'));

    if (!empty($import->errors)) {
        return response()->json([
            'message' => 'Some rows were skipped due to errors.',
            'errors' => $import->errors
        ], 422);
    }

    return response()->json([
        'message' => 'Prices updated successfully from Excel file.'
    ]);
}

    public function export()
{
    return Excel::download(new ProductPriceExport, 'product_prices.xlsx');
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
        // $this->authorize('update', $productPrice);
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

