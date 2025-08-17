<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'contact_info', 'price_type', 'created_by_user_id'];

    public function user() { return $this->belongsTo(User::class, 'created_by_user_id'); }
    public function baskets() { return $this->hasMany(Basket::class); }
    public function quoteRequests() { return $this->hasMany(QuoteRequest::class); }
}
