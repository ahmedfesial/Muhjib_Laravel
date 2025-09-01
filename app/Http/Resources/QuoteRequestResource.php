<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuoteRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client' => new ClientResource($this->whenLoaded('client')),
            'assigned_to' => $this->assigned_to,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'creator_name' => $this->creator?->name,

            'creator_products' => $this->creator?->products->map(function ($product) {
    return [
        'id' => $product->id,
        'name' => $product->name,
        'main_image' => $product->main_image,
        'specification' => $product->specification,
        'price' => $product->price,
    ];
}),
'products' => $this->products->map(function ($product) {
    return [
        'id' => $product->id,
        'name_en' => $product->name_en,
        'quantity' => $product->pivot->quantity,
        'price' => $product->pivot->price,
    ];
}),
        ];
    }
}
