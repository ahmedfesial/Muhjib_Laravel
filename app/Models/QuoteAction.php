<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\QuoteRequest;
use App\Models\User;

class QuoteAction extends Model
{
    use HasFactory;
    protected $table='quote_actions';

    protected $fillable = ['quote_request_id', 'user_id','price', 'action', 'note'];

    public function quoteRequest() {
        return $this->belongsTo(QuoteRequest::class);
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function forwardedToUser()
{
    return $this->belongsTo(User::class, 'forwarded_to_user_id');
}
public function priceChangeRequests()
{
    return $this->hasMany(PriceChangeRequest::class);
}
}
