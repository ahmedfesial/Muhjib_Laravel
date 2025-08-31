<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreQuoteRequestRequest;
use App\Http\Requests\UpdateQuoteRequestRequest;
use App\Http\Resources\QuoteRequestResource;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;
use App\Policies\QuoteRequestPolicy;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class QuoteRequestController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
{
    // $this->authorize('viewAny', QuoteRequest::class);
    $query = $this->filter($request);

    $data = QuoteRequestResource::collection($query->paginate(40));
    return response()->json([
        'message' => 'Quote Requests Retrieved Successfully',
        'data' => $data
    ], 200);
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
   $data = $request->only(['client_id', 'assigned_to', 'status']);
$data['created_by'] = Auth::id();

$quoteRequest = QuoteRequest::create($data);


    if ($request->has('products')) {
        foreach ($request->input('products') as $product) {
            $quoteRequest->products()->attach($product['product_id'], [
                'quantity' => $product['quantity'],
                'price' => $product['price'] ?? 0,
            ]);
        }
    }

    $quoteRequest->load(['creator', 'products']);

    return response()->json([
        'message' => 'Quote Request Created Successfully',
        'data' => new QuoteRequestResource($quoteRequest),
    ], 201);
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

public function approveQuote($id)
{
    $quote = QuoteRequest::findOrFail($id);

    if ($quote->status !== 'pending') {
        return response()->json(['message' => 'Quote is not pending.'], 400);
    }

    $quote->update(['status' => 'approved']);

    // لما الكوت يتقبل، يتم تفعيل العميل
    if ($quote->client) {
        $quote->client->update(['status' => 'approved']);
    }

    return response()->json(['message' => 'Quote approved and client activated.']);
}
public function rejectQuote($id)
{
    $quote = QuoteRequest::findOrFail($id);

    if ($quote->status !== 'pending') {
        return response()->json(['message' => 'Quote is not pending.'], 400);
    }

    $quote->update(['status' => 'rejected']);

    // العميل يفضل مخفي
    return response()->json(['message' => 'Quote rejected and client hidden.']);
}



    public function userQuoteRequests()
{
    $user = Auth::user();

    $quoteRequests = QuoteRequest::where('assigned_to', $user->id)->latest()->paginate(10);

    $data = QuoteRequestResource::collection($quoteRequests);

    return response()->json([
        'message' => 'Your Quote Requests Retrieved Successfully',
        'data' => $data,
    ], 200);
}
}
