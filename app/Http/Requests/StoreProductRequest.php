<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name_en' => 'nullable|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'features' => 'nullable|string',
            'main_color' => 'nullable|string|max:100',
            'brand_id' => 'nullable|exists:brands,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
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
            'price' => 'nullable|numeric|min:0',
            'is_visible' => 'boolean',
            'quantity' => 'required|integer|min:0',
        ];
    }
}

