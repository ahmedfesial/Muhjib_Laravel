<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Brand;
use App\Models\SubCategories;
use App\Models\ProductPrice;
use App\Models\BasketProduct;

class Product extends Model
{
    use HasFactory;
    protected $table='products';

    protected $fillable = [
        'name_en',
        'name_ar',
        'features',
        'main_color',
        'brand_id',
        'sub_category_id',
        'main_image',
        'pdf_hs',
        'pdf_msds',
        'pdf_technical',
        'hs_code',
        'sku',
        'pack_size',
        'dimensions',
        'capacity',
        'specification',
        'price',
        'is_visible',
    ];

    public function brand() {
        return $this->belongsTo(Brand::class);
    }

    public function subCategory() {
        return $this->belongsTo(SubCategories::class);
    }

    public function prices() {
        return $this->hasMany(ProductPrice::class);
    }
    public function basketProducts() { return $this->hasMany(BasketProduct::class); }
<<<<<<< HEAD
    public function getMainImageAttribute($value)
{
    return $value 
        ? asset('storage/' . $value) 
        : null;
}
=======
>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e
}
