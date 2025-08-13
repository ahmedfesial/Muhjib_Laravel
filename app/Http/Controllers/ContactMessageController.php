<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use App\Http\Requests\StoreContactMessageRequest;
use App\Http\Requests\UpdateContactMessageRequest;
use App\Http\Resources\ContactMessageResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class ContactMessageController extends Controller
{
    use AuthorizesRequests;
    public function __construct()
    {
        parent::__construct();
    }

    // User sends a message
    public function store(StoreContactMessageRequest $request)
    {
        $message = ContactMessage::create($request->validated());
        return response()->json(['message' => 'Message sent successfully.'], 201);
    }

    // Admin views all messages
    public function index()
    {
        $this->authorize('viewAny', ContactMessage::class);
        $messages = ContactMessage::latest()->paginate(10);
        $data =ContactMessageResource::collection($messages);
        return response()->json(['message' =>'Messages Retrieved Successfully'],200);
    }

    // Admin views a single message
    public function show(ContactMessage $contactMessage)
    {
        $this->authorize('view', $contactMessage);
        $data =new ContactMessageResource($contactMessage);
        return response()->json(['message' =>'Messages Retrieved Successfully'],200);
    }

    // Admin updates status/response
    public function update(UpdateContactMessageRequest $request, ContactMessage $contactMessage)
    {
        $this->authorize('update', $contactMessage);
        if(!$contactMessage){
            return response()->json([
                'message' => 'Message not found.',
            ], 404);
        }
        $contactMessage->update($request->validated());
        $data =new ContactMessageResource($contactMessage);
        return response()->json([
            'message' => 'Message updated successfully.',
            'data' => $data,
        ], 200);
    }

    // Admin deletes a message
    public function destroy(ContactMessage $contactMessage)
    {
        $this->authorize('delete', $contactMessage);
        if(!$contactMessage){
            return response()->json([
                'message' => 'Message not found.',
            ], 404);
        }
        $contactMessage->delete();
        return response()->json(['message' => 'Deleted successfully.'],200);
    }
}
