<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = ['name', 'logo','created_by', 'cover_image_start', 'cover_image_end', 'description'];

    protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    ];

    public function client()
    {
        return $this->hasOne(TemplateClient::class);
    }
public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function products()
{
    return $this->hasMany(TemplateProduct::class)->with('product'); // eager load
}
public function templateProducts()
{
    return $this->hasMany(TemplateProduct::class);
}
public function coverImages()
{
    return $this->hasMany(TemplateCoverImage::class);
}

public function startCoverImages()
{
    return $this->coverImages()->where('position', 'start');
}

public function endCoverImages()
{
    return $this->coverImages()->where('position', 'end');
}

}
