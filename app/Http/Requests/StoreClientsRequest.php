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
        'email' => 'nullable|email|unique:clients,email,' . $this->id,
        'phone' => 'nullable|string|max:20',
        'company' => 'nullable|string|max:255',
        'default_price_type' => 'nullable|string',
        'status' => 'nullable|in:pending,approved,rejected',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'created_by_user_id' => 'nullable|exists:users,id',
    ];
    }
}
