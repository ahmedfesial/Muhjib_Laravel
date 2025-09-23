<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuoteRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
        'client_id' => ['nullable', 'exists:clients,id'],
        'assigned_to' => ['nullable', 'exists:users,id'],
        'status' => 'nullable|in:pending,approved,rejected',
        'products' => ['nullable', 'array', 'min:1'],
        'products.*.name_en' => ['required', 'string', 'max:255'],
        'products.*.product_id' => ['required', 'exists:products,id'],
        'products.*.quantity' => ['required', 'integer', 'min:1'],
        'products.*.price' => ['nullable', 'numeric', 'min:0'],
    ];
    }
}
