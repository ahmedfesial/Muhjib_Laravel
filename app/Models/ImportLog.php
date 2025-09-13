<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ImportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_name',
        'status',
        'counts',
        'errors',
    ];

    protected $casts = [
        'counts' => 'array',
        'errors' => 'array',
    ];

    // ✅ أضف العلاقة دي:
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
