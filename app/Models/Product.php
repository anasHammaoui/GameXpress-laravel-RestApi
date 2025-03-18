<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name','slug','price','stock','category_id'];
    public function images(){
        return $this -> hasMany(Product_images::class);
    }

    public function carts(){
        return $this -> hasMany(Cart::class);
    }
}
