<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name_en', 'name_ar', 'color_code', 'image_url', 'brand_id'];

    public function brand() { return $this->belongsTo(Brand::class); }
    public function subCategories() { return $this->hasMany(SubCategory::class); }
}
