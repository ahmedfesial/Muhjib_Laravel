<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MainCategories;
use App\Models\Product;

class Brand extends Model
{
    use HasFactory;
    protected $table ='brands';

    protected $fillable = [
       'name_en',
    'name_ar',
    'logo',
    'short_description_en',
    'short_description_ar',
    'full_description_en',
    'full_description_ar',
    'background_image_url',
    'color_code',
    'catalog_pdf_url',
    ];

    public function mainCategories() {
        return $this->hasMany(MainCategories::class);
    }

    public function products() {
        return $this->hasMany(Product::class);
    }
}
