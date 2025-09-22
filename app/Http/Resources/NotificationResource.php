<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,

            // ✅ استخدام اسم الشركة من العلاقة مع العميل
            'content' => $this->type === 'client_approval_request'
                ? "Company " . optional($this->client)->company . " with non-default price type was created. Please review."
                : $this->content,


            'status' => $this->status,
            'approval_status' => $this->approval_status,
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'related_entity_id' => $this->related_entity_id,
            'created_at' => $this->created_at,

            'sender_name' => optional($this->sender)->name,
            'receiver_name' => optional($this->receiver)->name,
        ];
    }
}

