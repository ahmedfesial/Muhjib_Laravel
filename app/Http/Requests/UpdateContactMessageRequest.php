<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
{
    return [
        'status' => 'required|in:pending,responded,closed',
        'admin_response' => 'nullable|string',
    ];
}
}

