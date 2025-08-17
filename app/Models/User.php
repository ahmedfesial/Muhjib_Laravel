<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;



// 1. User
class User extends Authenticatable implements JWTSubject
{
    use HasFactory , Notifiable, HasRoles ;

    protected $table ='users';

    public function getJWTIdentifier() {
    return $this->getKey();
    }
    public function getJWTCustomClaims() {
    return [];
    }

    protected $fillable = ['name', 'email', 'password', 'phone', 'role', 'image'];
    protected $hidden = ['password', 'remember_token'];

    public function clients() { return $this->hasMany(Client::class, 'created_by_user_id'); }
    public function baskets() { return $this->hasMany(Basket::class, 'created_by'); }
    public function sentNotifications() { return $this->hasMany(Notification::class, 'sender_id'); }
    public function receivedNotifications() { return $this->hasMany(Notification::class, 'receiver_id'); }
    public function quoteActions() { return $this->hasMany(QuoteAction::class); }
}
