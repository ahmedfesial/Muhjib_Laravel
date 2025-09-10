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
        'main_colors',
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
        'quantity',

    ];
    protected $casts = [
    'main_colors' => 'array',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
];


    public function brand() {
        return $this->belongsTo(Brand::class);
    }

    public function subCategory() {
        return $this->belongsTo(SubCategories::class);
    }

public function price()
{
    return $this->hasOne(ProductPrice::class);
}

    public function basketProducts() { return $this->hasMany(BasketProduct::class); }
    public function getMainImageAttribute($value)
{
    return $value
        ? asset('storage/' . $value)
        : null;
}
public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
}

public function legends() {
    return $this->belongsToMany(Legend::class, 'legend_product', 'product_id', 'legend_id');
}

public function certificates() {
    return $this->belongsToMany(Certificate::class, 'certificate_product', 'product_id', 'certificate_id');
}

}
