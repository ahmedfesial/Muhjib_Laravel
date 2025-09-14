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

         $basketId = $request->input('basket_id');
    $products = $request->input('products');

    $createdProducts = [];

    foreach ($products as $productData) {
        $basketProduct = BasketProduct::create([
            'basket_id' => $basketId,
            'product_id' => $productData['product_id'],
            'quantity' => $productData['quantity'],
            'price' => $productData['price'] ?? 0,
        ]);

        $basketProduct->load('product');
        $createdProducts[] = new BasketProductResource($basketProduct);
    }

    return response()->json([
        'message' => 'Basket Products Created Successfully',
        'data' => $createdProducts,
    ], 201);

    }

    public function update(UpdateBasketProductsRequest $request, BasketProduct $basketProduct)
    {
        $basketProduct->update($request->validated());
            $basketProduct->load('product'); // تحميل بيانات المنتج
        $data=new BasketProductResource($basketProduct);
        return response()->json(['message'=>'Basket Products Updated Successfully', 'data' => $data],200);
    }

    public function destroy(BasketProduct $basketProduct)
    {
        $basketProduct->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
