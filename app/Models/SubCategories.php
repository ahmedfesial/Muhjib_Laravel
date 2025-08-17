<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name_en', 'name_ar', 'main_category_id'];

    public function mainCategory() { return $this->belongsTo(MainCategory::class); }
    public function products() { return $this->hasMany(Product::class); }
}
