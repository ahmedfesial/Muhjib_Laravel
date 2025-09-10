<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Legend extends Model
{
    protected $fillable = ['name', 'image'];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function getImageAttribute($value)
    {
        return $value ? asset('storage/' . $value) : null;
    }
}
