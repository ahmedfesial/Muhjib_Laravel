<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CatalogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'basket_id' => $this->basket_id,
            'template_id' => $this->template_id,
            'pdf_url' => $this->pdf_path ? asset('storage/' . $this->pdf_path) : null,
            'created_at' => $this->created_at,
            'basket' => new BasketResource($this->whenLoaded('basket')),
            'template' => new TemplateResource($this->whenLoaded('template')),
        ];
    }
}
