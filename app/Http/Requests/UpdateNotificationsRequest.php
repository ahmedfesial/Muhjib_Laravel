<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'sometimes|string|max:255',
            'sender_id' => 'sometimes|exists:users,id',
            'receiver_id' => 'sometimes|exists:users,id',
            'content' => 'sometimes|string',
            'status' => 'sometimes|string|in:unread,read',
            'related_entity_id' => 'nullable|integer'
        ];
    }
}
