<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    use HasFactory;
    protected $table='product_prices';

    protected $fillable = ['product_id', 'price_type', 'value'];

    public function product() { return $this->belongsTo(Product::class); }
}
