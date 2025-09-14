<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use App\Http\Resources\NotificationResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
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

    public function markAsRead(Notification $notification)
    {

        $notification->update(['status' => 'read']);

        return response()->json(['message' => 'Notification marked as read']);
    }

}
