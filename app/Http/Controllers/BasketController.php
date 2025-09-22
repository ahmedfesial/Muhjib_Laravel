<?php

namespace App\Http\Controllers;

use App\Models\Basket;
use App\Http\Requests\StoreBasketsRequest;
use App\Http\Requests\UpdateBasketsRequest;
use App\Http\Resources\BasketResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Events\BasketCreated;

class BasketController extends Controller
{
   public function index(Request $request)
{
    $baskets = Basket::with(['client', 'creator', 'basketProducts.product'])
        ->where('status', '!=', 'done') // 👈 استبعاد السلات المحوّلة
        ->paginate(10);

    $data = BasketResource::collection($baskets);

    return response()->json([
        'message' => 'Baskets Retrieved Successfully',
        'data' => $data
    ]);
}


public function filter(Request $request)
{
    $filters = $request->only([
        'status',
        'client_id',
    ]);

    $baskets = Basket::filter($filters)->paginate(10);
    $data = $baskets;
    return response()->json([
        'message' => 'Filtered Baskets Retrieved Successfully',
        'data' => $data
    ]);
}


    public function store(StoreBasketsRequest $request)
    {
    $createdBy = $request->input('created_by') ?? (Auth::check() ? Auth::id() : null);

       $basketData = $request->only([
        'name', 'client_id', 'include_price_flag', 'status'
    ]);

    $basketData['created_by'] = $createdBy;
    $products = $request->input('products');

    $basket = Basket::create($basketData);

    if (!empty($products)) {
    foreach ($products as $product) {
        $basket->basketProducts()->create([
            'product_id' => $product['product_id'],
            'quantity' => $product['quantity'],
            'price' => $product['price'] ?? 0,
        ]);
    }
    }

    $basket->load(['client', 'creator', 'basketProducts.product']);

    $data = new BasketResource($basket);
        event(new BasketCreated(Auth::user(), $basket));


    return response()->json([
        'message' => 'Basket Created Successfully',
        'data' => $data
    ], 201);
    }

    public function show($id)
    {
        $basket = Basket::with(['client', 'creator', 'basketProducts.product'])->find($id);
        if (!$basket) {
            return response()->json(['message' => 'Basket not found',], 404);
        }
        $data =new BasketResource($basket);
        return response()->json(['message'=>'Basket Retrieved Successfully', 'data' => $data],200);
    }

    public function update(UpdateBasketsRequest $request, Basket $basket)
    {
        $basket->update($request->validated());
        $data =new BasketResource($basket);
        return response()->json(['message'=>'Basket Updated Successfully', 'data' => $data],200);
    }

    public function destroy(Basket $basket)
    {
        $basket->delete();
        return response()->json(['message' => 'Basket deleted']);
    }

    public function changeStatus(Request $request, Basket $basket)
    {

        $request->validate(['status' => 'required|string|in:pending,in_progress,done']);

        $basket->status = $request->status;
        $basket->save();

        return response()->json(['message' => 'Status updated', 'status' => $basket->status]);
    }

public function getUserBaskets(User $user)
{
    $baskets = Basket::with(['client', 'products'])
        ->where('created_by', $user->id)
        ->where('status', '!=', 'done') // 👈 هنا كمان
        ->paginate(10);

    $data = BasketResource::collection($baskets);

    return response()->json([
        'message' => 'User Baskets Retrieved Successfully',
        'data' => $data,
    ]);
}


}


