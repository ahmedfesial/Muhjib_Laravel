<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Gate handled in controller
    }

    public function rules(): array
    {
        return [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:20',
        'company' => 'nullable|string|max:255',
        'default_price_type' => 'required|in:A,B,C'
        ];
    }
}
