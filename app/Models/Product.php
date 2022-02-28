<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable=[
        'nombre','category_id'
    ];

    protected $hidden=[

    ];

    protected $casts = [
        'created_at' => 'datetime:m-d-Y | h:i:s',
    ];

    //$producto->category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    //$producto->images
    // public function images()
    // {
    //     return $this->hasMany(Image::class);
    // }
}
