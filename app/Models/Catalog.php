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

<<<<<<< HEAD
    protected $fillable = ['basket_id', 'template_id', 'name', 'created_by', 'pdf_path'];

public function basket() {
    return $this->belongsTo(Basket::class);
}

public function template() {
    return $this->belongsTo(Template::class);
}

// 👇 Add creator relationship
public function creator() {
    return $this->belongsTo(User::class, 'created_by');
}

=======
    protected $fillable = ['basket_id', 'template_id', 'pdf_path'];

    public function basket() {
        return $this->belongsTo(Basket::class);
    }

    public function template() {
        return $this->belongsTo(Template::class);
    }
>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e
}
