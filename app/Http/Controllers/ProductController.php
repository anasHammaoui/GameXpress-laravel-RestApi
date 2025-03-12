<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(){
        // all products
        $products = Product::all();
        return response() -> json($products);
        return response() -> json([
            "Products" => $products
        ],200);
    }
}
