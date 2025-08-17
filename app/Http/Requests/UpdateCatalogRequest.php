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
<<<<<<< HEAD
            'name' => 'required|string|max:255',
        'basket_id' => 'required|exists:baskets,id',
        'template_id' => 'required|exists:templates,id',
=======
            'basket_id' => 'sometimes|exists:baskets,id',
            'template_id' => 'sometimes|exists:templates,id',
>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e
        ];
    }
}

