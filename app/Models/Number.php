<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Number extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'number',
        'message_id',
        'status'
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
