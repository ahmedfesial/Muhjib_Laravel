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
        'receiver_id' => 'nullable|exists:users,id',
        'type' => 'nullable|string|max:50',
        'content' => 'nullable|string|max:500',
        'related_entity_id' => 'nullable|integer',
    ];
    }
}
