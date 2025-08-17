<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuoteActionRequest;
use App\Http\Resources\QuoteActionResource;
use App\Models\QuoteAction;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class QuoteActionController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        // $this->authorize('viewAny', QuoteAction::class);
        $data =QuoteActionResource::collection(QuoteAction::latest()->paginate(10));
        return response()->json([
            'message' => 'Quote Actions Retrieved Successfully',
            'data' => $data
        ],200);
    }

    public function store(StoreQuoteActionRequest $request)
    {
        // $this->authorize('create', QuoteAction::class);
        $quoteAction = QuoteAction::create($request->validated());
        $data =new QuoteActionResource($quoteAction);
        return response()->json([
            'message' => 'Quote Action Created Successfully',
            'data' => $data
        ],201);
    }
    public function forwardtouser(){
        // Super Admin can forward client to any user
    }
}
