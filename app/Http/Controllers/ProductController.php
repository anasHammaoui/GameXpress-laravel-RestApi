<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class ProductController extends Controller
{
    // show all products
    public function index(){
        // all products
        if (auth('sanctum') -> user() -> can('view_products')){
            $products = Product::all();
        return response() -> json($products,200);
        }
        return abort(403);
    }
    // show a specific prouct
    public function show(Product $product){
        if (auth('sanctum')-> user() -> can('view_products')){

            return response() -> json($product) ;
        }
        return abort(403);
    }
    // add a product 
    public function store(Request $request){
        $validate = Validator::make($request -> all(),[
            "name" => "required",
            "price" => "required|numeric",
            "stock" => "required|integer",
            "category_id" => "required|integer"
        ]);
        if ($validate -> fails()){
            return response() -> json([
                "message"=>$validate -> errors()
            ],422);
        }
      $product =  Product::create([
            "name" => $request -> name,
            "slug" => Str::slug($request -> name),
            "price" => $request -> price,
            "stock" => $request -> stock,
            "category_id" => $request -> category_id
        ]);
        return response() -> json([
            "message" => "product created successfullly",
            "product" => $product
        ],200);
    }
}
