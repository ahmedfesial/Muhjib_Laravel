<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use App\Models\User;
use App\Models\QuoteAction;

class QuoteRequest extends Model
{
    use HasFactory;
    protected $table='quote_requests';

protected $fillable = [
    'client_id',
    'status',
    'assigned_to',
    'created_by',
    'client_email',
    'client_name',
    'client_phone',
    'client_company',
];

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function handler() {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
}

    public function actions() {
        return $this->hasMany(QuoteAction::class);
    }
    public function products()
{
    return $this->belongsToMany(Product::class, 'quote_request_products')
                ->withPivot('quantity', 'price')
                ->withTimestamps();
}


}
