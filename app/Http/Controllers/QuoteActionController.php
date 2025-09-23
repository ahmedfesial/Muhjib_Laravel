<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuoteActionRequest;
use App\Http\Resources\QuoteActionResource;
use App\Models\QuoteAction;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\PriceChangeRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Notification;
use App\Models\QuoteRequest;
use App\Http\Resources\QuoteRequestResource;
class QuoteActionController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $data =QuoteActionResource::collection(QuoteAction::latest()->paginate(10));
        return response()->json([
            'message' => 'Quote Actions Retrieved Successfully',
            'data' => $data
        ],200);
    }

    public function store(StoreQuoteActionRequest $request)
    {
        $quoteAction = QuoteAction::create($request->validated());
        $data =new QuoteActionResource($quoteAction);
        return response()->json([
            'message' => 'Quote Action Created Successfully',
            'data' => $data
        ],201);
    }

public function forwardToUser(Request $request, QuoteRequest $quote_request)
{
    $request->validate([
        'forwarded_to_user_id' => 'required|exists:users,id',
    ]);

    $currentUserId = Auth::id();
    $forwardToUserId = (int) $request->forwarded_to_user_id;

    // ✅ منع التوجيه لنفسك
    if ($forwardToUserId === $currentUserId) {
        return response()->json([
            'message' => 'You cannot forward a quote request to yourself.',
        ], 400);
    }

    // ✅ منع التوجيه لنفس الشخص اللي هو موجه له بالفعل
    if ($quote_request->assigned_to === $forwardToUserId) {
        return response()->json([
            'message' => 'Quote request is already assigned to this user.',
        ], 400);
    }

    // ✅ تم التحقق، نحدث البيانات
    $quote_request->assigned_to = $forwardToUserId;
    $quote_request->save();

    $quote_request->load(['creator', 'products']);
    $forwardedToUser = User::find($forwardToUserId);
    $data = new QuoteRequestResource($quote_request);

    return response()->json([
        'message' => 'Quote Request forwarded successfully',
        'forwarded_to' => $forwardedToUser->name,
        'data' => $data,
    ]);
}



    public function requestPriceChange(Request $request, $quoteId)
    {
    $request->validate([
        'requested_price' => 'required|numeric|min:0'
    ]);

    $user = Auth::user();

    $changeRequest = PriceChangeRequest::create([
        'quote_action_id' => $quoteId,
        'user_id' => $user->id,
        'requested_price' => $request->requested_price,
        'status' => 'pending'
    ]);
    $admins = User::whereIn('role', ['admin', 'super_admin'])->get();

    foreach ($admins as $admin) {
        Notification::create([
            'sender_id' => $user->id,
            'receiver_id' => $admin->id,
            'title' => 'Price Change Request',
            'body' => "{$user->name} requested to change the price for quote #{$quoteId} to {$request->requested_price}",
            'content' => 'System-generated notification for price change.',
            'status' => 'unread'
        ]);
    }
    $data=$changeRequest;
    return response()->json([
        'message' => 'Price change request submitted and awaiting approval.',
        'data' => $data
    ]);
    }
    public function approvePriceChange($requestId)
{
    $admin = Auth::user();
    if (!in_array($admin->role, ['admin', 'super_admin'])) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $request = PriceChangeRequest::findOrFail($requestId);

    $quote = QuoteAction::findOrFail($request->quote_action_id);
    $quote->price = $request->requested_price;
    $quote->save();

    $request->status = 'approved';
    $request->save();
    Notification::create([
        'sender_id' => $admin->id,
        'receiver_id' => $request->user_id,
        'title' => 'Price Change Approved',
        'body' => "Your request to change the price to {$request->requested_price} was approved by {$admin->name}.",
        'content' => 'System-generated notification for price change.',
        'status' => 'unread'
    ]);
    $data =$quote;
    return response()->json([
        'message' => 'Price change approved and applied.',
        'data' => $data
    ]);
}
public function rejectPriceChange($requestId)
{
    $admin = Auth::user();
    if (!in_array($admin->role, ['admin', 'super_admin'])) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $request = PriceChangeRequest::findOrFail($requestId);

    $request->status = 'rejected';
    $request->save();

    Notification::create([
        'sender_id' => $admin->id,
        'receiver_id' => $request->user_id,
        'title' => 'Price Change Rejected',
        'body' => "Your request to change the price to {$request->requested_price} was rejected by {$admin->name}.",
        'content' => 'System-generated notification for price change.',
        'status' => 'unread'
    ]);

    return response()->json(['message' => 'Price change request rejected.']);
}
}
