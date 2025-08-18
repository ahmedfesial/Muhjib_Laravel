<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Legand extends Model
{
    protected $table = 'legand';
    protected $fillable = ['image'];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image);
    }
}
