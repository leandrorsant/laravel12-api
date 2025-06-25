<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Str;
use Validator;

class BlogCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = BlogCategory::get();

        return response()->json([
            "status" => "success",
            "data" => $categories
        ], 200);
    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Validade
        $validator = Validator::make($request->all(),[
            "name" => "required|min:4|unique:blog_categories,name",
        ]);

        if($validator->fails()){
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()
            ],400);
        }

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        BlogCategory::create($data);

         return response()->json([
            "status" => "success",
            "message" => "Category created successfully",
            "data" => $data
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $categories = BlogCategory::find($id);

        if (!$categories) {
            return response()->json([
                "status" => "error",
                "message" => "Category not found"
            ], 404);
        }

        return response()->json([
            "status" => "success",
            "data" => $categories
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = BlogCategory::find($id);
        
        //Check if student exists in the database
        if(!$category){
            return response()->json([
                "status" => "error",
                "message" => "category not found, cannot update"
            ], 404);
        }

        //Validade
        $validator = Validator::make($request->all(),[
            "name" => "required|min:4|unique:blog_categories,name,".$id,
        ]);

        if($validator->fails()){
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()
            ],400);
        }

        $category->name = $request->name;
        $category->slug = Str::slug(title: $request->name);

        $category->save();

         //Return response

        return response()->json([
            "status" => "sucess",
            "message" => "Category updated successfully",
            "data" => $category
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = BlogCategory::find($id);

        if(!$category){
            return response()->json([
                "status" => "error",
                "message" => "Cannot delete category because the provided id doesn't match any records"
            ]);
        }

        BlogCategory::destroy($id);

        return response()->json([
                "status" => "success",
                "message" => "Category deleted successfully"
        ]);
    }
}
