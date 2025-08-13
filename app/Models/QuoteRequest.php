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

    protected $fillable = ['client_id', 'assigned_to', 'status'];

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function handler() {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function actions() {
        return $this->hasMany(QuoteAction::class);
    }
}
