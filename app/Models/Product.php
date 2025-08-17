<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name_en', 'name_ar', 'features', 'main_color', 'sub_category_id', 'main_image', 'pdf_hs', 'pdf_msds', 'pdf_technical', 'hs_code', 'sku', 'pack_size', 'dimensions', 'capacity', 'specification', 'price', 'is_visible'];

    public function subCategory() { return $this->belongsTo(SubCategory::class); }
    public function prices() { return $this->hasMany(ProductPrice::class); }
    public function basketProducts() { return $this->hasMany(BasketProduct::class); }
}
