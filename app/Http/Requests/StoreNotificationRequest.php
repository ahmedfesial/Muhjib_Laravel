<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
        'receiver_id' => 'required|exists:users,id',
        'type' => 'required|string|max:50',
        'content' => 'required|string|max:500',
        'related_entity_id' => 'nullable|integer',
    ];
    }
}
