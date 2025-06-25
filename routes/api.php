<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BlogCategoryController;
use App\Http\Controllers\API\BlogPostController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\LikeController;
use App\Http\Controllers\API\StudentApiController;
use App\Http\Controllers\API\TestApiController;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::get('/test', [TestApiController::class, 'test'])->name('test-api');

Route::apiResource('/students',StudentApiController::class);

Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    //Blog category routes
    Route::apiResource('categories',BlogCategoryController::class)->middleware('role:admin');

    //Blog post routes
    Route::apiResource('posts', BlogPostController::class)->middleware('role:admin,author');
    Route::post('/blog-post-image', [BlogPostController::class, 'blogPostImage'])->name('blog-post-image')->middleware('role:admin,author');

    //Blog post like/dislike routes
    Route::post('/post/react', [LikeController::class, 'react'])->name('blog-post-like');

    //Comments api route
    Route::apiResource('comments', CommentController::class);

    //Restrict access to get all comments to admin role only
    Route::get('/comments', [CommentController::class, 'index'])->middleware('role:admin');

    //Change status of a comment
    Route::patch('/comments/change-status/{comment_id}', [CommentController::class, 'changeStatus'])->name('comments.change-status')->middleware('role:admin');

    //Get single blog post comments
    Route::get('/posts/{post_id}/comments', [CommentController::class, 'getPostComments'])->name('post.comments');
});

Route::get('/categories', [BlogCategoryController::class, 'index'])->name('blog_categories.index');
Route::get('/posts', [BlogPostController::class, 'react'])->name('react');
Route::get('/posts/reactions/{id}', [LikeController::class, 'reactions'])->name('post.reaction.count');