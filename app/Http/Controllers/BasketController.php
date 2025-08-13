<?php

namespace App\Http\Controllers;

use App\Models\Basket;
use App\Http\Requests\StoreBasketsRequest;
use App\Http\Requests\UpdateBasketsRequest;
use App\Http\Resources\BasketResource;
use Illuminate\Http\Request;
use App\Http\Resources\ClientResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BasketController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        // $this->authorize('viewAny', Basket::class);
        $baskets = Basket::with(['client', 'creator', 'products'])->paginate(10);
        $data = BasketResource::collection($baskets);
        return response()->json(['message'=>'Baskets Retrieved Successfully', 'data' => $data],200);
    }

    public function store(StoreBasketsRequest $request)
    {
        // $this->authorize('create', Basket::class);
        $basket = Basket::create($request->validated());
        $data =new BasketResource($basket);
        return response()->json(['message'=>'Baskets Created Successfully', 'data' => $data],201);
    }

    public function show(Basket $basket)
    {
        $this->authorize('view', $basket);
        $basket = Basket::find($basket);
        $data =new BasketResource($basket->load(['client', 'creator', 'products']));
        return response()->json(['message'=>'Basket Retrieved Successfully', 'data' => $data],200);
    }

    public function update(UpdateBasketsRequest $request, Basket $basket)
    {
        $this->authorize('update', $basket);
        $basket = Basket::find($basket);
        if(!$basket){
            return response()->json([
                'message' => 'Brand not found.',
            ], 404);
        }
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
        $this->authorize('update', $basket);

        $request->validate(['status' => 'required|string|in:pending,in_progress,done']);

        $basket->status = $request->status;
        $basket->save();

        return response()->json(['message' => 'Status updated', 'status' => $basket->status]);
    }
}

