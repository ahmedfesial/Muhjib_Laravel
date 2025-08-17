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
    public function index(Request $request)
    {
        // $this->authorize('viewAny', Basket::class);
         $filters = $request->only([
        'status',
        'client_id',
        'created_by',
        'include_price_flag',
        'created_from',
        'created_to'
    ]);

    $baskets = Basket::with(['client', 'creator', 'products'])
                    ->filter($filters)
                    ->paginate(10);
        $data = BasketResource::collection($baskets);
        return response()->json(['message'=>'Baskets Retrieved Successfully', 'data' => $data],200);
    }

    public function scopeFilter($query, $filters)
{
    if (isset($filters['status'])) {
        $query->where('status', $filters['status']);
    }

    if (isset($filters['client_id'])) {
        $query->where('client_id', $filters['client_id']);
    }

    if (isset($filters['created_by'])) {
        $query->where('created_by', $filters['created_by']);
    }

    if (isset($filters['include_price_flag'])) {
        $query->where('include_price_flag', $filters['include_price_flag']);
    }

    if (isset($filters['created_from'])) {
        $query->whereDate('created_at', '>=', $filters['created_from']);
    }

    if (isset($filters['created_to'])) {
        $query->whereDate('created_at', '<=', $filters['created_to']);
    }

    return $query;
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
        $this->authorize('view', $id);
        $basket = Basket::find($id);
        if (!$basket) {
            return response()->json(['message' => 'Basket not found',], 404);
        }
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

