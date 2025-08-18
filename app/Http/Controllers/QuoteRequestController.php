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
    public function index(Request $request)
    {
        // $this->authorize('viewAny', QuoteRequest::class);
            $query = $this->filter($request);

        $data =QuoteRequestResource::collection(QuoteRequest::latest()->paginate(10));
        return  response()->json([
            'message' => 'Quote Requests Retrieved Successfully',
            'data' => $data
        ],200);
    }

    private function filter(Request $request)
{
    $query = QuoteRequest::query();

    if ($request->filled('client_id')) {
        $query->where('client_id', $request->client_id);
    }

    if ($request->filled('assigned_to')) {
        $query->where('assigned_to', $request->assigned_to);
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    return $query->latest(); // latest by created_at
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

    public function show($id)
    {
        // $this->authorize('view', $quoteRequest);
        $quoteRequest = QuoteRequest::find($id);
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
        // $this->authorize('update', $quoteRequest);
        // if(!$quoteRequest){
        //     return response()->json([
        //         'message'=>'Quote Request Not Found'
        //     ],404);
        // }
        $quoteRequest->update($request->validated());
        $data =new QuoteRequestResource($quoteRequest);
        return response()->json([
            'message' => 'Quote Request Updated Successfully',
            'data' => $data
        ],200);
    }
}
