<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'main_category_id' => $this->main_category_id,
            'name_en' => $this->name_en,
            'name_ar' => $this->name_ar,
            'created_at' => $this->created_at,
             // روابط الصور الكاملة
            'cover_image' => $this->cover_image ? asset('storage/' . $this->cover_image) : null,
            'background_image' => $this->background_image ? asset('storage/' . $this->background_image) : null,

            // هل في صورة مرفوعة
            'has_cover_image' => !empty($this->cover_image),
            'has_background_image' => !empty($this->background_image),
        ];
    }
}
