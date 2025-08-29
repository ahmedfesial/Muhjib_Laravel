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
        // $this->authorize('viewAny', Notification::class);
        $notifications = Notification::where('receiver_id', Auth::id())->latest()->get();
        $data = NotificationResource::collection($notifications);
        return response()->json(['message' => 'Notifications Retrieved Successfully', 'data' => $data], 200);
    }

    public function store(StoreNotificationRequest $request)
    {
        // $this->authorize('create', Notification::class);
        $notification = Notification::create([$request->validated(),'sender_id' => Auth::id(),'status'=>'unread']);
        $data = new NotificationResource($notification);
        return response()->json(['message' => 'Notification Created Successfully', 'data' => $data], 201);
    }

    // public function show(Notification $notification)
    // {
    //     $this->authorize('view', $notification);
    //     $notification = Notification::find($notification);
    //     if(!$notification){
    //         return response()->json([
    //             'message' => 'Notidication not found.',
    //         ], 404);
    //     }
    //     $data = new NotificationResource($notification);
    //     return response()->json(['message' => 'Notification Retrieved Successfully', 'data' => $data], 200);
    // }

    // public function update(UpdateNotificationRequest $request, Notification $notification)
    // {
    //     $this->authorize('update', $notification);
    //     $notification = Notification::find($notification);
    //     if(!$notification){
    //         return response()->json([
    //             'message' => 'Notidication not found.',
    //         ], 404);
    //     }
    //     $notification->update($request->validated());
    //     $data = new NotificationResource($notification);
    //     return response()->json(['message' => 'Notification Updated Successfully', 'data' => $data], 200);
    // }

    // public function destroy(Notification $notification)
    // {
    //     $this->authorize('delete', $notification);
    //     $notification = Notification::find($notification);
    //     if(!$notification){
    //         return response()->json([
    //             'message' => 'Notidication not found.',
    //         ], 404);
    //     }
    //     $notification->delete();
    //     return response()->json(['message' => 'Notification Deleted Successfully'], 200);
    // }
    public function markAsRead(Notification $notification)
    {
        // $this->authorize('update', $notification);

        $notification->update(['status' => 'read']);

        return response()->json(['message' => 'Notification marked as read']);
    }

}
