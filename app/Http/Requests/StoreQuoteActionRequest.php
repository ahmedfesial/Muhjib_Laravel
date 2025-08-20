<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuoteActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quote_request_id' => ['required', 'exists:quote_requests,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'forwarded_to_user_id' => ['nullable', 'exists:users,id'],
            'action' => ['required', 'in:created,updated,transferred,approved,rejected'],
            'note' => ['nullable', 'string'],
        ];
    }
}
