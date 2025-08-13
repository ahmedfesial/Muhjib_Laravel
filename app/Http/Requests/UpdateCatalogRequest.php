<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCatalogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'basket_id' => 'sometimes|exists:baskets,id',
            'template_id' => 'sometimes|exists:templates,id',
        ];
    }
}

