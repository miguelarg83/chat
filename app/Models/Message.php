<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    // protected $casts = [
    //     'created_at' => 'datetime:m-d-Y | h:i:s',
    // ];

    protected $fillable=[
        'mensaje','user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
