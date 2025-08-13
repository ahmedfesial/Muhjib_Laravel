<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BasketProduct;
use App\Models\Client;
use App\Models\User;
use App\Models\Catalog;

class Basket extends Model
{
    use HasFactory;
    protected $table ='baskets';

    protected $fillable = ['client_id', 'created_by', 'include_price_flag', 'status'];

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function products() {
        return $this->hasMany(BasketProduct::class);
    }
    public function catalog() { return $this->hasOne(Catalog::class); }
}
