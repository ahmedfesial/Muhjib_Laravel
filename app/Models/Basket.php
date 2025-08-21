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

    protected $fillable = ['name' ,'client_id', 'created_by', 'include_price_flag', 'status'];

    public function basketProducts()
{
    return $this->hasMany(BasketProduct::class, 'basket_id');
}
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
    public function scopeFilter($query, $filters)
    {
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        return $query;
    }
}
