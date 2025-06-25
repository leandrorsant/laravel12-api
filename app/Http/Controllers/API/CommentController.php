<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Validator;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Get all comments
        $comments = Comment::get();

        return response()->json([
            "status" => "success",
            "count" => count($comments),
            "data" => $comments
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /* 
        Assuming the comments table has the following structure:
        | id         | bigint unsigned                       | NO   | PRI | NULL    | auto_increment |
        | post_id    | int                                   | NO   |     | NULL    |                |
        | user_id    | int                                   | NO   |     | NULL    |                |
        | parent_id  | int                                   | NO   |     | 0       |                |
        | content    | text                                  | NO   |     | NULL    |                |
        | status     | enum('pending','approved','rejected') | NO   |     | pending |                |
        | created_at | timestamp                             | YES  |     | NULL    |                |
        | updated_at | timestamp                             | YES  |     | NULL    |                |
        +------------+---------------------------------------+

        and the user is authenticated, you can implement the store method like this:
        */
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer|exists:blog_posts,id',
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|integer|exists:comments,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $data['post_id'] = $request->post_id;
        $data['user_id'] = auth()->id(); // Get the authenticated user's ID
        $data['content'] = $request->content;

        Comment::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Comment created and waiting for admin approval.'
        ], 201);
    }

    /**
     * Update the status of a comment.
     */
    public function changeStatus(Request $request, $comment_id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,approved,rejected'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }
        $comment = Comment::findOrFail($comment_id);
        $comment->status = $request->status;
        $comment->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Comment status updated successfully.',
            'data' => $comment
        ], 200);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Get comments for a specific blog post.
     */
    public function getPostComments($post_id){
        $comments = Comment::where('post_id', $post_id)->get();

        return response()->json([
            'status' => 'success',
            'count' => count($comments),
            'data' => $comments
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
