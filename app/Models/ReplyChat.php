<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReplyChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'from',
        'pesan'
    ];
}
