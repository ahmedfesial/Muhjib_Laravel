<?php

namespace App\Http\Controllers;

use App\Models\BasketProduct;
use App\Http\Requests\StoreBasketProductsRequest;
use App\Http\Requests\UpdateBasketProductsRequest;
use App\Http\Resources\BasketProductResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BasketProductsController extends Controller
{
    use AuthorizesRequests;
    public function store(StoreBasketProductsRequest $request)
    {
        // $this->authorize('create', BasketProduct::class);

        $basketProduct = BasketProduct::create($request->validated());
        $data =new BasketProductResource($basketProduct);
        return response()->json(['message'=>'Basket Products Created Successfully', 'data' => $data],201);
        
    }

    public function update(UpdateBasketProductsRequest $request, BasketProduct $basketProduct)
    {
        $this->authorize('update', $basketProduct);
        $basketProduct = BasketProduct::find($basketProduct);
        if(!$basketProduct){
            return response()->json([
                'message' => 'Brand not found.',
            ], 404);
        }
        $basketProduct->update($request->validated());
        $data=new BasketProductResource($basketProduct);
        return response()->json(['message'=>'Basket Products Updated Successfully', 'data' => $data],200);
    }

    public function destroy(BasketProduct $basketProduct)
    {
        // $this->authorize('delete', $basketProduct);
        // $basketProduct = BasketProduct::find($basketProduct);
        // if(!$basketProduct){
        //     return response()->json([
        //         'message' => 'Brand not found.',
        //     ], 404);
        // }
        $basketProduct->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
