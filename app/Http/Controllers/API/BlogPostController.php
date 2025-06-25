<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BlogPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = BlogPost::with('seo_data')->get();

        return response()->json([
            'status' => 'success',
            'count' => count($posts),
            'data' => $posts
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'category_id' => 'required|numeric',
            'title' => 'required',
            'content' => 'required',
            'thumbnail' => 'nullable|image|max:2048',
            'meta_title' => 'required',
            'meta_description' => 'required',
            'meta_keywords' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ], 400);
        }

        // Check if user is same as loggedin user
        $loggedInUser = Auth::user();

        if($loggedInUser->id != $request->user_id){
            return response()->json([
                'status' => 'fail',
                'message' => 'Un-authorized access (Check if user is same as loggedin user)'
            ], 400);
        }

        // check if category id is exits in DB
        $category = BlogCategory::find($request->category_id);
        if(!$category){
            return response()->json([
                'status' => 'fail',
                'message' => 'Category not found',
            ], 404);
        }

        $imagePath = null;
        if($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()){
            $file = $request->file('thumbnail');

            // Generate unique file name
            $fileName = time().'_'.$file->getClientOriginalName();

            //Move file into storage
            $file->move(public_path('storage/posts'), $fileName);

            //Save image path into our database
            $imagePath = "storage/posts/".$fileName;
        }

        $data['title'] = $request->title;
        $data['slug'] = Str::slug($request->title);
        $data['user_id'] = $request->user_id;
        $data['category_id'] = $request->category_id;
        $data['content'] = $request->content;
        $data['excerpt'] = $request->excerpt;
        $data['thumbnail'] = $imagePath ? $imagePath : null;
        if(Auth::user()->role == 'admin'){
            $data['status'] = 'published';
        }
        if(Auth::user()->role == 'admin' || Auth::user()->role == 'author')
        $data['published_at'] = date('Y-m-d H:i:s');

        $blogPost = BlogPost::create($data); // It will new blog posts

        $postId = $blogPost->id; 
        $seoData['post_id'] = $postId;
        $seoData['meta_title'] = $request->meta_title;
        $seoData['meta_description'] = $request->meta_description;
        $seoData['meta_keywords'] = $request->meta_keywords;

        Seo::create($seoData); // It will create data in seo data

        return response()->json([
            'status' => 'success',
            'message' => 'Blog post created successfully'
        ], 201);


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

     public function update(Request $request, int $id)
    {
        // Check Blog Post
        $blogPost = BlogPost::find($id);
        if(!$blogPost){
            return response()->json([
                'status' => 'fail',
                'message' => 'No Blog Post Found'
            ], 404);
        }


        // Validate Input
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'category_id' => 'required|numeric',
            'title' => 'required',
            'content' => 'required',
            'meta_title' => 'required',
            'meta_description' => 'required',
            'meta_keywords' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ], 400);
        }

        // Check if user is same as loggedin user
        $loggedInUser = Auth::user();

        // check if category id is exits in DB
        $category = BlogCategory::find($request->category_id);
        if(!$category){
            return response()->json([
                'status' => 'fail',
                'message' => 'Category not found',
            ], 404);
        }

        // Check additional condition to restrict authorized edit
        if($loggedInUser->id == $blogPost->user_id || Auth::user()->role == 'admin'){
            $blogPost->category_id = $request->category_id;
            $blogPost->user_id = $request->user_id;
            $blogPost->title = $request->title;
            $blogPost->slug = Str::slug($request->title);
            $blogPost->content = $request->content;
            $blogPost->excerpt = $request->excerpt;
            $blogPost->status = $request->status;
            $blogPost->save(); // IT will update the record in database

            $seoData = Seo::where('post_id', $blogPost->id)->first();

            $seoData->meta_title = $request->meta_title;
            $seoData->meta_description = $request->meta_description;
            $seoData->meta_keywords = $request->meta_keywords;

            $seoData->save(); // It will update the seo information in DB


            return response()->json([
                'status' => 'success',
                'message' => 'Blog Post editted successfully!'
                ], 201);
        }else{
            return response()->json([
                'status' => 'fail',
                'message' => 'You are not allowed to perform this task'
                ], 403);
        }

    

    }

    public function blogPostImage(Request $request, int $id){
        // Check Blog Post
        $blogPost = BlogPost::find($id);
        if(!$blogPost){
            return response()->json([
                'status' => 'fail',
                'message' => 'No Blog Post Found'
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'thumbnail' => 'nullable|image|max:2048'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ], 400);
        }

        // Check if user is same as loggedin user
        $loggedInUser = Auth::user();

        if($loggedInUser->id == $blogPost->user_id || Auth::user()->role == 'admin'){
            // Image Upload
            $imagePath = null;
            if($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()){
            $file = $request->file('thumbnail');

            // Generate unique file name
            $fileName = time().'_'.$file->getClientOriginalName();

            //Move file into storage
            $file->move(public_path('storage/posts'), $fileName);

            //Save image path into our database
            $imagePath = "storage/posts/".$fileName;
            }

            $blogPost->thumbnail = isset($imagePath) ? $imagePath : $blogPost->thumbnail;
            $blogPost->save(); // It will update the record in database

            return response()->json([
            'status' => 'success',
            'message' => 'Blog image updated successfully'
            ], 201);
        }else{
            return response()->json([
                'status' => 'fail',
                'message' => 'You are not allowed to perform this task'
                ], 403);
        }

        
    }

   

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Check blog post
        $blogPost = BlogPost::find($id);
        if(!$blogPost){
            return response()->json([
                'status' => 'fail',
                'message' => 'No Blog Post Found'
            ], 404);
        }
        // Check if user is same as loggedin user
        $loggedInUser = Auth::user();
        if($loggedInUser->id == $blogPost->user_id || Auth::user()->role == 'admin'){
            $blogPost->delete(); // It will delete record in database

            return response()->json([
                'status' => 'success',
                'messgae' => 'Post deleted successfully'
            ], 201);

        }else{
            return response()->json([
                'status' => 'fail',
                'message' => 'You are not allowed to perform this task'
                ], 403);
        }
        
    }
}