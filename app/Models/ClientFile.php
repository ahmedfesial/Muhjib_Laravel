<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientFile extends Model
{
    protected $fillable = ['client_id', 'file_name', 'file_path', 'file_type'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}

