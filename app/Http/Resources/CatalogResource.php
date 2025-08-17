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
        'name' => $this->name,
        'basket' => new BasketResource($this->whenLoaded('basket')),
        'template' => new TemplateResource($this->whenLoaded('template')),
        'pdf_url' => $this->pdf_path ? asset('storage/' . $this->pdf_path) : null,
        'creator' => $this->creator ? $this->creator->name : null,
        'created_at' => $this->created_at,
        ];
    }
}
