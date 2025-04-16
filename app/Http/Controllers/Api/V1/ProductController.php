<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Product_images;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class ProductController extends Controller
{
    // show all products
    public function index(){
        // all products

            $products = Product::with(["images","category"])->get();
        return response() -> json([
           "products" => $products,
           "out Of stock" => Product::where('stock',0) -> get()
        ],200);
   
        return response() -> json(["message" => "failed to get all products"],403);
    }
    // show a specific prouct
    public function show(Product $product){
            $product->load(["category", "images"]);
            return response()->json([
                "product" => $product,
            ], 200);
        return response() -> json(['message' => 'failed to get data, you\'re not loged in'],403);
    }
    // add a product 
    public function store(Request $request){
        $validate = Validator::make($request -> all(),[
            "name" => "required",
            "price" => "required|numeric",
            "stock" => "required|integer",
            "category_id" => "required|integer",
            "images" => "required|array",
            "images.*" => "required|image|mimes:jpeg,png,jpg,gif,svg|max:5120"
        ]);
        if ($validate -> fails()){
            return response() -> json([
                "message"=>$validate -> errors()
            ],422);
        }
        // stock images names in the array to store it in the ddb
        $images = [];

        // for each loop to iterate throgh images
        foreach($request ->file('images') as $image){
            $imageName = time(). '_'. uniqid().'.' . $image -> getClientOriginalExtension();
            $image -> storeAs('products_images',$imageName,'public');
            $imageName = Storage::url('products_images/'.$imageName);
            array_push($images,$imageName);
        }
        $product =  Product::create([
            "name" => $request -> name,
            "slug" => Str::slug($request -> name),
            "price" => $request -> price,
            "stock" => $request -> stock,
            "category_id" => $request -> category_id
        ]);
        foreach($images as $index => $value){
           if ($index=== 0){
            Product_images::create([
                'image_url' => $value,
                'product_id' => $product -> id,
                'is_primary' => true
            ]);
           } else {
            Product_images::create([
                'image_url' => $value,
                'product_id' => $product -> id,
                'is_primary' => false
            ]);
           }
        }
      
        return response() -> json([
            "message" => "product created successfullly",
            "product" => $product
        ],200);
    }
    public function test(Request $request){
        dd($request);
    }
    // update a product
    public function update(Product $product, Request $request){
        $validate = Validator::make($request -> all(),[
            "name" => "required",
            "price" => "required|numeric",
            "stock" => "required|integer",
            "category_id" => "required|integer",
            "images" => "required|array",
            "images.*" => "required|image|mimes:jpeg,png,jpg,gif,svg|max:5120"
        ]);
        if ($validate -> fails()){
            return response() -> json([
                "message"=>$validate -> errors()
            ],422);
        }
        // edit the product
          // edit product in the db
          $product -> name = $request -> name;
          $product -> slug = Str::slug($request -> name);
          $product -> price =  $request -> price;
          $product -> stock = $request -> stock;
          $product -> category_id = $request -> category_id;
          $product -> save();

        if ($request -> hasFile('images')){
    // stock images names in the array to store it in the ddb
        $images = [];

        // for each loop to iterate throgh images
            foreach($request ->file('images') as $image){
                $imageName = time(). '_'. uniqid().'.' . $image -> getClientOriginalExtension();
                $image -> storeAs('products_images',$imageName,'public');
                 $imageName = Storage::url('products_images/'.$imageName);
                array_push($images,$imageName);
            }
            // Delete old images from storage and database
            foreach($product->images as $image) {
                // Remove file from storage
                if (file_exists(storage_path('app/public/' . $image->image_url))) {
                    unlink(storage_path('app/public/' . $image->image_url));
                }
                // Delete record from database
                $image->delete();
            }
            foreach($images as $index => $value){
                if ($index=== 0){
                 Product_images::create([
                     'image_url' => $value,
                     'product_id' => $product -> id,
                     'is_primary' => true
                 ]);
                } else {
                 Product_images::create([
                     'image_url' => $value,
                     'product_id' => $product -> id,
                     'is_primary' => false
                 ]);
                }
             }
        }
      
        return response() -> json([
            "message" => "product has been edited successfullly",
            "product" => $product
        ],200);
    }
    // delete a product 
    public function destroy($id){
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->images()->delete();
        $product->delete();


        return response()->json(['message' => 'Product deleted successfully']);
    }
}
