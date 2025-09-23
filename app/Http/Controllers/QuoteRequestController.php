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
    $clientData = $request->input('client');

    // تحقق من أن client موجودة ومصنفة كمصفوفة وتحتوي على email
    if (!is_array($clientData) || !isset($clientData['email'])) {
        return response()->json([
            'message' => 'Client information is missing or invalid.',
        ], 422);
    }

    // ابحث عن العميل باستخدام البريد الإلكتروني
    $client = \App\Models\Client::where('email', $clientData['email'])->first();

    // إذا لم يكن موجودًا، أنشئ عميلًا جديدًا
    if (!$client) {
        $client = \App\Models\Client::create([
            'name' => $clientData['name'] ?? null,
            'email' => $clientData['email'],
            'phone' => $clientData['phone'] ?? null,
            'company' => $clientData['company'] ?? null,
            'status' => 'pending',
            'created_by_user_id' => Auth::id(),
        ]);
    }

    // إنشاء الطلب وربطه بالعميل
    $quoteRequest = QuoteRequest::create([
        'client_id' => $client->id,
        'status' => $request->input('status', 'pending'),
        'assigned_to' => Auth::user()->last_assigned_to ?? null,
        'created_by' => Auth::id(),
    ]);

    // ربط المنتجات بالطلب
    if ($request->has('products') && is_array($request->input('products'))) {
        foreach ($request->input('products') as $product) {
            if (isset($product['product_id'], $product['quantity'])) {
                $quoteRequest->products()->attach($product['product_id'], [
                    'quantity' => $product['quantity'],
                    'price' => $product['price'] ?? 0,
                ]);
            }
        }
    }

    // تحميل العلاقات المرتبطة
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

    if ($quote->client) {
        $quote->client->update(['status' => 'approved']);
    }

    $basket = \App\Models\Basket::create([
        'name' => 'Basket for Quote ' . $quote->id,
        'client_id' => $quote->client_id,
        'status' => 'pending',
        'created_by' => Auth::id(),
        'include_price_flag' => true, // حسب تصميمك
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
        'message' => 'Quote approved, client activated, and basket created.',
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

    $quote = QuoteRequest::findOrFail($id);

    $quote->update([
        'assigned_to' => $request->forward_to,
    ]);

    // نحفظ آخر شخص السوبر أدمن وجه له كوتيشن
    $user = Auth::user();

    if (!$user) {
        return response()->json(['message' => 'User not authenticated.'], 401);
    }

    if ($user instanceof \App\Models\User) {
        $user->last_assigned_to = $request->forward_to;
        if (!$user->save()) {
        return response()->json(['message' => 'Failed to update user assignment.'], 500);
        }
    }

    return response()->json(['message' => 'Quote forwarded successfully.']);
}


}
