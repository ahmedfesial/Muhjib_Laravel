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
        'client' => new ClientResource($this->whenLoaded('client')),
        'creator' => new UserResource($this->whenLoaded('creator')),
        'products' => BasketProductResource::collection($this->whenLoaded('products')),
        'include_price_flag' => $this->include_price_flag,
        'status' => $this->status,
        'created_at' => $this->created_at,
        ];
    }
}
