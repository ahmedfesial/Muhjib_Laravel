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
<<<<<<< HEAD
        'email' => 'nullable|email|unique:clients,email,' . $this->id,
        'phone' => 'nullable|string|max:20',
        'company' => 'nullable|string|max:255',
        'default_price_type' => 'nullable|string',
        'status' => 'nullable|in:active,inactive',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
=======
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:20',
        'company' => 'nullable|string|max:255',
        'default_price_type' => 'required|in:A,B,C'
>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e
        ];
    }
}
