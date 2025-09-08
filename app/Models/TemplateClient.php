<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateClient extends Model
{
    protected $fillable = [
        'template_id',
        'client_id',
        'client_name',
        'email',
        'phone',
        'address'
    ];

    // علاقة مع جدول templates
    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    // علاقة مع جدول clients
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
