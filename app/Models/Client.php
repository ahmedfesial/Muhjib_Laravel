<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Basket;
use App\Models\User;
use App\Models\QuoteRequest;

class Client extends Model
{
    use HasFactory;
    protected $table ='clients';

    protected $fillable = ['created_by_user_id', 'name', 'email', 'phone', 'company', 'default_price_type'];

    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function baskets() {
        return $this->hasMany(Basket::class);
    }
    public function quoteRequests() { return $this->hasMany(QuoteRequest::class); }
    
}
