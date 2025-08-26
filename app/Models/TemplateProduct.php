<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateProduct extends Model
{
    protected $fillable = ['product_id', 'template_id', 'name', 'description', 'price', 'image'];


    public function template()
    {
        return $this->belongsTo(Template::class);
    }
    public function product()
{
    return $this->belongsTo(Product::class);
}
}
