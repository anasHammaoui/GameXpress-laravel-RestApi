<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product_images extends Model
{
    protected $fillable = ['image_url','product_id','is_primary'];
    public function product(){
        return $this -> belongsTo(Product::class);
    }
}
