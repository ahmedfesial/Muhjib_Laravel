<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name_en', 'name_ar', 'logo', 'short_description_en', 'short_description_ar', 'full_description_en', 'full_description_ar', 'background_image_url', 'color_code', 'catalog_pdf_url'];

    public function mainCategories() { return $this->hasMany(MainCategory::class); }
}
