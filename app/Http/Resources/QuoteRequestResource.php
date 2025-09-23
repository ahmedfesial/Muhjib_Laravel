<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuoteRequestResource extends JsonResource
{
    public function toArray($request)
{
    return [
        'id' => $this->id,
        'client' => $this->client
            ? new ClientResource($this->client)
            : [
                'id' => null,
                'name' => $this->client_name ?? 'Unknown',
                'email' => $this->client_email,
                'phone' => $this->client_phone,
                'company' => $this->client_company,
                'status' => 'not_registered',
            ],
        'assigned_to' => $this->assigned_to,
        'status' => $this->status,
        'created_at' => $this->created_at,
        'creator_name' => $this->creator?->name,
        'creator_products' => [], // لو عندك logic تاني هنا ضيفه
        'products' => $this->whenLoaded('products', function () {
            return $this->products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name_en' => $product->name_en,
                    'quantity' => $product->pivot->quantity,
                    'price' => number_format($product->pivot->price, 2),
                ];
            });
        }),
    ];
}
}
