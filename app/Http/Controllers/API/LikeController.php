<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    //Likes and Dislikes
    public function react(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer|exists:blog_posts,id', // Assuming you have a blog_posts table
            'status' => 'required|integer|in:1,2', // 1 = Like, 2 = Dislike
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ], 400);
        }

        $userId = auth()->id(); // Get the authenticated user's ID
        $postId = $request->post_id;
        $status = $request->status;

        $like = Like::where('user_id', $userId)
            ->where('post_id', $postId)
            ->first();
        
        if($like){
            if($like->status == $status) {
                //Same reaction - remove reaction
                $like->delete();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Reaction removed successfully.'
                ], 200);
            }else{
                //Update reaction
                $like->status = $status;
                $like->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Reaction updated successfully.'
                ], 200);
            }
            
        } else {
            Like::create([
                'user_id' => $userId,
                'post_id' => $postId,
                'status' => $status
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Reaction added successfully.'
            ], 201);
        }

    }

    // Get reactions count for a post
    public function reactions($id){
        $reactions = Like::where('post_id', $id)->get();
        $likeCount = $reactions->where('status', 1)->count();
        $dislikeCount = $reactions->where('status', 2)->count();

        return response()->json([
            'status' => 'success',
            'post_id' => $id,
            'likes' => $likeCount,
            'dislikes' => $dislikeCount,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
