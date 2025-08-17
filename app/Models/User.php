<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// 1. User
class User extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'password', 'phone', 'role'];

    public function clients() { return $this->hasMany(Client::class, 'created_by_user_id'); }
    public function baskets() { return $this->hasMany(Basket::class, 'created_by'); }
    public function sentNotifications() { return $this->hasMany(Notification::class, 'sender_id'); }
    public function receivedNotifications() { return $this->hasMany(Notification::class, 'receiver_id'); }
    public function quoteActions() { return $this->hasMany(QuoteAction::class); }
}
