<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
         return [
            'id' => $this->id,
            'name_en' => $this->name_en,
            'name_ar' => $this->name_ar,
            'features' => $this->features,
            'main_color' => $this->main_color,
            'brand_id' => $this->brand_id,
            'sub_category_id' => $this->sub_category_id,
<<<<<<< HEAD
            'main_image' => $this->main_image ? asset('storage/' . $this->main_image) : null,
            'pdf_hs' => $this->pdf_hs ? asset('storage/' . $this->pdf_hs) : null,
            'pdf_msds' => $this->pdf_msds ? asset('storage/' . $this->pdf_msds) : null,
            'pdf_technical' => $this->pdf_technical ? asset('storage/' . $this->pdf_technical) : null,
=======
            'main_image' => $this->main_image,
            'pdf_hs' => $this->pdf_hs,
            'pdf_msds' => $this->pdf_msds,
            'pdf_technical' => $this->pdf_technical,
>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e
            'hs_code' => $this->hs_code,
            'sku' => $this->sku,
            'pack_size' => $this->pack_size,
            'dimensions' => $this->dimensions,
            'capacity' => $this->capacity,
            'specification' => $this->specification,
            'price' => $this->price,
            'is_visible' => $this->is_visible,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    protected function getImageUrls(): array
    {
    return collect($this->images)->map(function ($image) {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        return $disk->url($image);
    })->toArray();
    }
}
