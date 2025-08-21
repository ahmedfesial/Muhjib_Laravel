<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BasketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'client_name' => $this->client?->name,
            'creator_name' => $this->creator?->name,
            'product_count' => $this->products->count(),
            'basket_products' => BasketProductResource::collection($this->whenLoaded('basketProducts')),
            'include_price_flag' => $this->include_price_flag,
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
