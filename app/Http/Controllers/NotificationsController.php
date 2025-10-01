<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use App\Http\Resources\NotificationResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
class NotificationsController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $notifications = Notification::where('receiver_id', Auth::id())->latest()->get();
        $data = NotificationResource::collection($notifications);
        return response()->json(['message' => 'Notifications Retrieved Successfully', 'data' => $data], 200);
    }

    public function store(StoreNotificationRequest $request)
    {
        $notification = Notification::create([$request->validated(),'sender_id' => Auth::id(),'status'=>'unread']);
        $data = new NotificationResource($notification);
        return response()->json(['message' => 'Notification Created Successfully', 'data' => $data], 201);
    }

public function markAllAsRead()
{
    Auth::user()->unreadNotifications->markAsRead();

    return response()->json(['message' => 'All notifications marked as read']);
}

    public function approve(Notification $notification)
{

    $notification->update(['approval_status' => 'approved']);

    return response()->json(['message' => 'Notification approved successfully']);
}

public function reject(Notification $notification)
{

    $notification->update(['approval_status' => 'rejected']);

    return response()->json(['message' => 'Notification rejected successfully']);
}


}
