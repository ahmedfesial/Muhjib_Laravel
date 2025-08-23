<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubCategoriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'main_category_id' => 'nullable|exists:main_categories,id',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
        ];
    }
}
