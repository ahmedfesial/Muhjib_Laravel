<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategories extends Model
{
    use HasFactory;
    protected $table='sub_categories';

    protected $fillable = ['name_en', 'name_ar', 'main_category_id'];

    public function mainCategory() { return $this->belongsTo(MainCategories::class); }
    public function products() { return $this->hasMany(Product::class); }
}
