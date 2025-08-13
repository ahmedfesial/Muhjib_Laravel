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
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|email|unique:clients,email,' . $this->client->id,
            'phone' => 'sometimes|string|max:20|unique:clients,phone,' . $this->client->id,
            'company_name' => 'nullable|string|max:255',
            'default_price_type' => 'sometimes',
        ];
    }
}