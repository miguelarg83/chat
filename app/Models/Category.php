<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email' , 'alias' , 'web' , 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    //$category->parent
    public function parent()
    {
        return $this->belongsTo('App\Models\Category');
    }

    //$category->categories
    public function categories()
    {
        return $this->hasMany('App\Models\Category','parent_id');
    }

    // $categoria->user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // $categoria->products
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
