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
        ];
    }
}
