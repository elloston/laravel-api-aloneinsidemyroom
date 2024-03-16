<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;

class CommentController extends Controller
{
    // /**
    //  * Display a listing of the resource.
    //  */
    public function index()
    {
        $comments = Comment::orderBy('created_at', 'desc')
            ->with(['user', 'replies'])
            ->withCount([
                'likes',
                'dislikes',
                'replies' => function ($query) {
                    $query->orderBy('created_at', 'desc')
                        ->withCount(['likes', 'dislikes'])
                        ->take(5);
                },
            ])
            ->paginate(10);

        return CommentResource::collection($comments);
    }

    public function getCommentsForPost($postId)
    {
        $comments = Comment::where('post_id', $postId)
            ->orderBy('created_at', 'desc')
            ->withCount(['likes', 'dislikes', 'replies'])
            ->with('user')
            ->paginate(2);

        return CommentResource::collection($comments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CommentRequest $request)
    {
        $comment = new Comment($request->validated());
        $request->user()->comments()->save($comment);
        $comment->load(['user'])->loadCount('replies');

        return response()->json(new CommentResource($comment), 201);
    }

    // /**
    //  * Display the specified resource.
    //  */
    public function show(Comment $comment)
    {
        $comment->load(['user', 'replies']);

        return response()->json(new CommentResource($comment), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CommentRequest $request, Comment $comment)
    {
        $comment->update($request->validated());
        $comment->load(['user', 'replies']);

        return response()->json(new CommentResource($comment), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response()->json(null, 204);
    }
}
