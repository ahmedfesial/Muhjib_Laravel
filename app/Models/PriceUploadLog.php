<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
class PriceUploadLog extends Model
{
    use HasFactory;
    protected $table = 'price_upload_logs';

    protected $fillable = ['uploaded_by', 'file_name', 'products_updated'];

    public function user() {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
