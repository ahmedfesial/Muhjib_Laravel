<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'description' => $this->description,
            'date' => $this->created_at->format('M d, g:i A') // Apr 12, 2:39 PM
        ];
    }
}
