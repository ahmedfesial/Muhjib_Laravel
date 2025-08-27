<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_en' => 'sometimes|required|string|max:255',
            'name_ar' => 'sometimes|required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'short_description_en' => 'nullable|string',
            'short_description_ar' => 'nullable|string',
            'full_description_en' => 'nullable|string',
            'full_description_ar' => 'nullable|string',
            'background_image_url' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:4096',
            'color_code' => 'nullable|string|max:7',
            'catalog_pdf_url' => 'nullable|mimes:pdf|max:10000',
        ];
    }
}


