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
<<<<<<< HEAD
        return [
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'short_description_en' => 'nullable|string',
            'short_description_ar' => 'nullable|string',
            'full_description_en' => 'nullable|string',
            'full_description_ar' => 'nullable|string',
            'background_image_url' => 'nullable|image|max:4096',
            'color_code' => 'nullable|string|max:7',
            'catalog_pdf_url' => 'nullable|file|mimes:pdf|max:10240',
        ];

=======
        return (new StoreBrandRequest)->rules();
>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e
    }
}

