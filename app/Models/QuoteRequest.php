<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    use HasFactory;
    protected $table='quote_requests';

    protected $fillable = ['client_id', 'status', 'assigned_to'];

    public function client() { return $this->belongsTo(Client::class); }
    public function assignedUser() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function actions() { return $this->hasMany(QuoteAction::class); }
}
