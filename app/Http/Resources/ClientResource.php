<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'default_price_type' => $this->default_price_type,
<<<<<<< HEAD
            'logo' => $this->logo ? asset('storage/' . $this->logo) : null,
            'status' => $this->status,
            'created_by_user_id' => $this->created_by_user_id,
=======
            'created_at' => $this->created_at,
>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e
        ];
    }
}
