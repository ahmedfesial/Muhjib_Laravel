<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Basket;
use App\Models\Template;
class Catalog extends Model
{
    use HasFactory;
    protected $table ='catalogs';

    protected $fillable = ['basket_id', 'template_id', 'pdf_path'];

    public function basket() {
        return $this->belongsTo(Basket::class);
    }

    public function template() {
        return $this->belongsTo(Template::class);
    }
}
