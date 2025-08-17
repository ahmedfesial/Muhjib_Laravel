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
<<<<<<< HEAD
        'name' => $this->name,
        'basket' => new BasketResource($this->whenLoaded('basket')),
        'template' => new TemplateResource($this->whenLoaded('template')),
        'pdf_url' => $this->pdf_path ? asset('storage/' . $this->pdf_path) : null,
        'creator' => $this->creator ? $this->creator->name : null,
        'created_at' => $this->created_at,
=======
            'basket_id' => $this->basket_id,
            'template_id' => $this->template_id,
            'pdf_url' => $this->pdf_path ? asset('storage/' . $this->pdf_path) : null,
            'created_at' => $this->created_at,
            'basket' => new BasketResource($this->whenLoaded('basket')),
            'template' => new TemplateResource($this->whenLoaded('template')),
>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e
        ];
    }
}
