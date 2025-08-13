<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SubCategories;

class MainCategories extends Model
{
    use HasFactory;
    protected $table='main_categories';

    protected $fillable = ['brand_id', 'name_en', 'name_ar', 'color_code', 'image_url'];

    public function brand() {
        return $this->belongsTo(Brand::class);
    }

    public function subCategories() {
        return $this->hasMany(SubCategories::class);
    }
}
