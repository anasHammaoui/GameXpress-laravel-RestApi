<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       if (auth('sanctum') -> user() -> can('view_categories')){
        $categories = Category::all();
        return response() -> json([
            'categries' => $categories,
        ],200);
       }
       return response() -> json([
       'message' => 'You dont have acces to this page'
    ],403);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // if (auth('sanctum') -> user() -> can('create_categories')){
            $validate = Validator::make($request -> all(),[
                'name' => 'required|min:4' 
            ]);
            if ($validate -> fails()){
                return response() -> json(['message' => $validate -> errors()],422);
            }
           $category = Category::create([
                'name'=> $request -> name,
                'slug' => Str::slug($request -> name)
            ]);
            return response() -> json(['message'=> 'category created successfully', 'category' => $category],200);
        // }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (auth('sanctum')->user()->can('edit_categories')) {
            $validate = Validator::make($request->all(), [
                'name' => 'required|min:4'
            ]);
            
            if ($validate->fails()) {
                return response()->json(['message' => $validate->errors()], 422);
            }
            
            $category = Category::find($id);
            
            if (!$category) {
                return response()->json(['message' => 'Category not found'], 404);
            }
            
            $category->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name)
            ]);
            
            return response()->json([
                'message' => 'Category updated successfully', 
                'category' => $category
            ], 200);
        }
        
        return response()->json([
            'message' => 'You don\'t have access to update categories'
        ], 403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (auth('sanctum')->user()->can('delete_categories')) {
            $category = Category::find($id);
            
            if (!$category) {
                return response()->json(['message' => 'Category not found'], 404);
            }
            
            $category->delete();
            
            return response()->json(['message' => 'Category deleted successfully'], 200);
        }
        
        return response()->json([
            'message' => 'You don\'t have access to delete categories'
        ], 403);
    }
}
