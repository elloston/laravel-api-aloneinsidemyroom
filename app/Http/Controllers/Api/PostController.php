<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::orderBy('created_at', 'desc')
            ->with([
                'user',
                'comments' => function ($query) {
                    $query->orderBy('created_at', 'desc')
                        ->withCount(['likes', 'dislikes', 'replies'])
                        ->take(5);
                }
            ])
            ->withCount(['likes', 'dislikes', 'comments'])
            ->paginate(10);

        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        $post = new Post($request->validated());
        $request->user()->posts()->save($post);
        $post->load(['user', 'comments', 'reactions']);

        return response()->json(new PostResource($post), 201);
    }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(Post $post)
    // {
    //     $post->load(['user', 'comments', 'reactions']);

    //     return response()->json(new PostResource($post), 200);
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, Post $post)
    {
        $post->update($request->validated());
        $post->load(['user', 'comments', 'reactions']);

        return response()->json(new PostResource($post), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json(null, 204);
    }
}
