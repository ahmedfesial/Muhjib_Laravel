<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteAction extends Model
{
    use HasFactory;
    protected $table='quote_actions';

    protected $fillable = ['quote_request_id', 'user_id', 'action'];

    public function quoteRequest() { return $this->belongsTo(QuoteRequest::class); }
    public function user() { return $this->belongsTo(User::class); }
}
