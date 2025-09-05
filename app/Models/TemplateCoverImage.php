<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateCoverImage extends Model
{
    protected $fillable = ['template_id', 'path', 'position'];

    public function template()
    {
        return $this->belongsTo(Template::class);
    }
}
