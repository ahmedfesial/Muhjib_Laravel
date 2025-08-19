<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatalogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
           'name' => 'required|string|max:255',
            'basket_id' => 'required|exists:baskets,id',
            'template_id' => 'required|exists:templates,id',
        ];
    }
}
