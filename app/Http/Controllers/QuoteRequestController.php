<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreQuoteRequestRequest;
use App\Http\Requests\UpdateQuoteRequestRequest;
use App\Http\Resources\QuoteRequestResource;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;
use App\Policies\QuoteRequestPolicy;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class QuoteRequestController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        // $this->authorize('viewAny', QuoteRequest::class);
        $data =QuoteRequestResource::collection(QuoteRequest::latest()->paginate(10));
        return  response()->json([
            'message' => 'Quote Requests Retrieved Successfully',
            'data' => $data
        ],200);
    }

    public function store(StoreQuoteRequestRequest $request)
    {
        // $this->authorize('create', QuoteRequest::class);
        $quote = QuoteRequest::create($request->validated());
        $data=new QuoteRequestResource($quote);
        return response()->json([
            'message' => 'Quote Request Retrieved Successfully',
            'data' => $data
        ],201);
    }

    public function show(QuoteRequest $quoteRequest)
    {
        $this->authorize('view', $quoteRequest);
        if(!$quoteRequest){
            return response()->json([
                'message'=>'Quote Request Not Found'
            ],404);
        }
        $data =new QuoteRequestResource($quoteRequest);
        return response()->json([
            'message' => 'Quote Request Retrieved Successfully',
            'data' => $data
        ],200); 
    }

    public function update(UpdateQuoteRequestRequest $request, QuoteRequest $quoteRequest)
    {
        $this->authorize('update', $quoteRequest);
        if(!$quoteRequest){
            return response()->json([
                'message'=>'Quote Request Not Found'
            ],404);
        }
        $quoteRequest->update($request->validated());
        $data =new QuoteRequestResource($quoteRequest);
        return response()->json([
            'message' => 'Quote Request Updated Successfully',
            'data' => $data
        ],200);
    }
}
