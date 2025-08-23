<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMainCategoriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brand_id' => 'nullable|exists:brands,id',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'image_url' => 'nullable|image|max:2048',
            'color_code' => 'nullable|string|max:7', // Assuming color is a hex code
        ];
    }
}


