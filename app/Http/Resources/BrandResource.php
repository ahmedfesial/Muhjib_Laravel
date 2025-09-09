<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BrandResource extends JsonResource
{
    public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'name_en' => $this->name_en,
        'name_ar' => $this->name_ar,
        'logo' => $this->logo ? asset(Storage::url($this->logo)) : null,
        'short_description_en' => $this->short_description_en,
        'short_description_ar' => $this->short_description_ar,
        'full_description_en' => $this->full_description_en,
        'full_description_ar' => $this->full_description_ar,
        'background_image_url' => $this->background_image_url ? asset(Storage::url($this->background_image_url)) : null,
        'color_code' => $this->color_code,
        'catalog_pdf_url' => $this->catalog_pdf_url ? asset(Storage::url($this->catalog_pdf_url)) : null,
        'main_categories' => $this->whenLoaded('mainCategories'),
        'created_at' => $this->created_at,
    ];
}

}

