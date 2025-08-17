<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name_en' => 'sometimes|required|string|max:255',
            'name_ar' => 'sometimes|required|string|max:255',
            'features' => 'nullable|string',
            'main_color' => 'nullable|string|max:100',
            'brand_id' => 'sometimes|required|exists:brands,id',
            'sub_category_id' => 'sometimes|required|exists:sub_categories,id',
            'main_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'pdf_hs' => 'nullable|file|mimes:pdf',
            'pdf_msds' => 'nullable|file|mimes:pdf',
            'pdf_technical' => 'nullable|file|mimes:pdf',
            'hs_code' => 'nullable|string|max:50',
            'sku' => 'nullable|string|max:100',
            'pack_size' => 'nullable|string|max:100',
            'dimensions' => 'nullable|string|max:100',
            'capacity' => 'nullable|string|max:100',
            'specification' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'is_visible' => 'boolean',
        ];
    }
}
