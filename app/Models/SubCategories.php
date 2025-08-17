<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MainCategories;
class SubCategories extends Model
{
    use HasFactory;
    protected $table='sub_categories';

    protected $fillable = ['main_category_id', 'name_en', 'name_ar'];

    public function mainCategory() {
        return $this->belongsTo(MainCategories::class);
    }

    public function products() {
        return $this->hasMany(Product::class);
    }
}
