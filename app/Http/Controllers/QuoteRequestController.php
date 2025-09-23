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
use App\Models\Client;
use App\Models\User;
class QuoteRequestController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
{
    $query = $this->filter($request);

$data = QuoteRequestResource::collection(
    $query->with('client')->paginate(40)
);
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
    $email = $request->input('client_email');

    // حاول تجيب العميل
    $client = Client::where('email', $email)->first();
    $clientId = $client?->id;

    $data = $request->only(['status']);

    // استخدم client_id لو موجود
    $data['client_id'] = $clientId;

    // خزن بيانات العميل المؤقتة
    $data['client_email'] = $email;
    $data['client_name'] = $request->input('client_name', 'Unknown');
    $data['client_phone'] = $request->input('client_phone');
    $data['client_company'] = $request->input('client_company');

    $data['assigned_to'] = Auth::user()->last_assigned_to ?? null;
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

    $quoteRequest->load(['creator', 'products', 'client']);

    return response()->json([
        'message' => 'Quote Request Created Successfully',
        'data' => new QuoteRequestResource($quoteRequest),
    ], 201);
}

    public function show($id)
    {
$quoteRequest = QuoteRequest::with('client')->find($id);
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
        $quoteRequest->update($request->validated());
        $data =new QuoteRequestResource($quoteRequest);
        return response()->json([
            'message' => 'Quote Request Updated Successfully',
            'data' => $data
        ],200);
    }
public function approveQuote($id)
{
    $quote = QuoteRequest::with(['client', 'products'])->findOrFail($id);

    if ($quote->status !== 'pending') {
        return response()->json(['message' => 'Quote is not pending.'], 400);
    }

    $quote->update(['status' => 'approved']);

    // تسجيل العميل لو مش موجود
    if (!$quote->client) {
        $client = Client::create([
            'email' => $quote->client_email,
            'name' => $quote->client_name ?? 'Unknown',
            'phone' => $quote->client_phone,
            'company' => $quote->client_company,
            'status' => 'approved',
            'created_by_user_id' => $quote->created_by,
        ]);

        // اربط العميل بالكوتيشن
        $quote->update(['client_id' => $client->id]);
    } else {
        $quote->client->update(['status' => 'approved']);
    }

    // أنشئ الباسكت
    $basket = \App\Models\Basket::create([
        'name' => 'Basket for Quote ' . $quote->id,
        'client_id' => $quote->client_id,
        'status' => 'pending',
        'created_by' => Auth::id(),
        'include_price_flag' => true,
    ]);

    foreach ($quote->products as $product) {
        $basket->basketProducts()->create([
            'product_id' => $product->id,
            'quantity' => $product->pivot->quantity,
            'price' => $product->pivot->price ?? 0,
        ]);
    }

    $basket->load(['client', 'creator', 'basketProducts.product']);

    return response()->json([
        'message' => 'Quote approved, client created, and basket generated.',
        'basket' => new \App\Http\Resources\BasketResource($basket),
    ]);
}


public function rejectQuote($id)
{
    $quote = QuoteRequest::findOrFail($id);

    if ($quote->status !== 'pending') {
        return response()->json(['message' => 'Quote is not pending.'], 400);
    }

    $quote->update(['status' => 'rejected']);

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

public function forwardQuote(Request $request, $id)
{
    $request->validate([
        'forward_to' => 'required|exists:users,id',
    ]);

    $currentUserId = Auth::id();
    $forwardToUserId = (int) $request->forward_to;

    // منع التوجيه لنفس الشخص
    if ($forwardToUserId === $currentUserId) {
        return response()->json([
            'message' => 'You cannot forward a quote request to yourself.',
        ], 400);
    }

    // جلب الكوتيشن
    $quoteRequest = QuoteRequest::findOrFail($id);

    // منع التوجيه لنفس الشخص لو هو أصلاً مستلمها
    if ($quoteRequest->assigned_to === $forwardToUserId) {
        return response()->json([
            'message' => 'Quote request is already assigned to this user.',
        ], 400);
    }

    // تنفيذ التوجيه
    $quoteRequest->assigned_to = $forwardToUserId;
    $quoteRequest->save();

    $quoteRequest->load(['creator', 'products']);
    $forwardedToUser = User::find($forwardToUserId);
    $data = new QuoteRequestResource($quoteRequest);

    return response()->json([
        'message' => 'Quote Request forwarded successfully',
        'forwarded_to' => $forwardedToUser->name,
        'data' => $data,
    ]);
}



}
