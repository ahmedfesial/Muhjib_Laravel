<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Catalog;
class Template extends Model
{
    use HasFactory;
    protected $table='templates';

     protected $fillable = ['name', 'file_path', 'description'];

    public function catalogs() {
        return $this->hasMany(Catalog::class);
    }
}
