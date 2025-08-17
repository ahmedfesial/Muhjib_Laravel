<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuoteActionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quote_request_id' => $this->quote_request_id,
            'user_id' => $this->user_id,
            'action' => $this->action,
            'note' => $this->note,
            'created_at' => $this->created_at,
        ];
    }
}
