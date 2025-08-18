<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceChangeRequest extends Model
{
    protected $table ='price_change_requests';
    protected $fillable = [
        'quote_action_id',
        'user_id',
        'requested_price',
        'status',
    ];

    public function quoteAction()
    {
        return $this->belongsTo(QuoteAction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}