<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
         return [
            'id' => $this->id,
            'name_en' => $this->name_en,
            'name_ar' => $this->name_ar,
            'features' => $this->features,
'main_colors' => collect($this->main_colors)->map(function ($color) {
    if (Str::startsWith($color, ['http://', 'https://'])) {
        return $color; // صورة URL
    }

    if (Str::endsWith($color, ['.jpg', '.jpeg', '.png', '.webp'])) {
        return asset('storage/' . $color); // صورة داخل الستوريج
    }

    return $color; // نص عادي (مثل: red)
}),

            'brand_id' => $this->brand_id,
            'sub_category_id' => $this->sub_category_id,
            'main_image' => $this->main_image
    ? (Str::startsWith($this->main_image, ['http://', 'https://'])
        ? $this->main_image
        : asset('storage/' . $this->main_image))
    : null,
            'pdf_hs' => $this->pdf_hs ? asset('storage/' . $this->pdf_hs) : null,
            'pdf_msds' => $this->pdf_msds ? asset('storage/' . $this->pdf_msds) : null,
            'pdf_technical' => $this->pdf_technical ? asset('storage/' . $this->pdf_technical) : null,
            'hs_code' => $this->hs_code,
            'sku' => $this->sku,
            'pack_size' => $this->pack_size,
            'dimensions' => $this->dimensions,
            'capacity' => $this->capacity,
            'specification' => $this->specification,
            'price' => ProductPriceResource::collection($this->whenLoaded('price')),
            'is_visible' => $this->is_visible,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'quantity' => $this->quantity,
            'certificates' => collect($this->certificates)->map(function ($path) {
    return asset('storage/' . $path);
}),
'legends' => collect($this->legends)->map(function ($path) {
    return asset('storage/' . $path);
}),
        ];
    }
    private function isImagePath($value)
{
    return is_string($value) && Str::endsWith($value, ['.jpg', '.jpeg', '.png', '.webp']);
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
