<?php

namespace App\Http\Controllers;

use App\Models\Basket;
use App\Http\Requests\StoreBasketsRequest;
use App\Http\Requests\UpdateBasketsRequest;
use App\Http\Resources\BasketResource;
use Illuminate\Http\Request;
use App\Http\Resources\ClientResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\User;

class BasketController extends Controller
{
    public function index(Request $request)
    {
        // $this->authorize('viewAny', Basket::class);

        $baskets = Basket::with(['client', 'creator', 'products'])->paginate(10);
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
        // $this->authorize('create', Basket::class);
        $basket = Basket::create($request->validated());
        $data =new BasketResource($basket);
        return response()->json(['message'=>'Baskets Created Successfully', 'data' => $data],201);
    }

    public function show($id)
    {
        // $this->authorize('view', $id);
        $basket = Basket::find($id);
        if (!$basket) {
            return response()->json(['message' => 'Basket not found',], 404);
        }
        $data =new BasketResource($basket);
        return response()->json(['message'=>'Basket Retrieved Successfully', 'data' => $data],200);
    }

    public function update(UpdateBasketsRequest $request, Basket $basket)
    {
        // $basket = Basket::find($basket);
        // if(!$basket){
        //     return response()->json([
        //         'message' => 'Brand not found.',
        //     ], 404);
        // }
        $basket->update($request->validated());
        $data =new BasketResource($basket);
        return response()->json(['message'=>'Basket Updated Successfully', 'data' => $data],200);
    }

    public function destroy(Basket $basket)
    {
        // $this->authorize('delete', $basket);
        // $basket = Basket::find($basket);
        // if(!$basket){
        //     return response()->json([
        //         'message' => 'Brand not found.',
        //     ], 404);
        // }
        $basket->delete();
        return response()->json(['message' => 'Basket deleted']);
    }

    public function changeStatus(Request $request, Basket $basket)
    {
        // $this->authorize('update', $basket);

        $request->validate(['status' => 'required|string|in:pending,in_progress,done']);

        $basket->status = $request->status;
        $basket->save();

        return response()->json(['message' => 'Status updated', 'status' => $basket->status]);
    }

    public function getUserBaskets(User $user)
{
    $baskets = Basket::with(['client', 'products'])
        ->where('created_by', $user->id)
        ->paginate(10);

    $data = BasketResource::collection($baskets);

    return response()->json([
        'message' => 'User Baskets Retrieved Successfully',
        'data' => $data,
    ]);
}

}


