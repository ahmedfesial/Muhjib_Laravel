<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
protected $fillable = ['user_id', 'event_type', 'description'];
public $timestamps = true;
protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
];


public function user()
{
    return $this->belongsTo(\App\Models\User::class);
}
}
