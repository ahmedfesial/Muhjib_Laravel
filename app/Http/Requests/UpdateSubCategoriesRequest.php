<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubCategoriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'main_category_id' => 'sometimes|exists:main_categories,id',
            'name_en' => 'sometimes|string|max:255',
            'name_ar' => 'sometimes|string|max:255',
        ];
    }
}
