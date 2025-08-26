<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateClient extends Model
{
    protected $fillable = ['template_id', 'client_name', 'email', 'phone', 'address'];

    public function template()
    {
        return $this->belongsTo(Template::class);
    }
}
