<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PriceUploadLogResource extends JsonResource
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
        'file_name' => $this->file_name,
        'products_updated' => $this->products_updated,
        'uploaded_by' => $this->uploaded_by,
        'user' => new UserResource($this->whenLoaded('user')),
        'created_at' => $this->created_at->toDateTimeString(),
    ];
    }
}
