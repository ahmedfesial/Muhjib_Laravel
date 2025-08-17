<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
        'email' => 'nullable|email|unique:clients,email,' . $this->id,
        'phone' => 'nullable|string|max:20',
        'company' => 'nullable|string|max:255',
        'default_price_type' => 'nullable|string',
        'status' => 'nullable|in:active,inactive',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    }
}