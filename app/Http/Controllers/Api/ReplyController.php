<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReplyRequest;
use App\Http\Resources\ReplyResource;
use App\Models\Reply;

class ReplyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $replies = Reply::orderBy('created_at', 'desc')
            ->with(['user'])
            ->paginate(10);

        return ReplyResource::collection($replies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReplyRequest $request)
    {
        $reply = new Reply($request->validated());
        $request->user()->replies()->save($reply);

        return response()->json(new ReplyResource($reply), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reply $reply)
    {
        $reply->load(['user']);

        return response()->json(new ReplyResource($reply), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReplyRequest $request, Reply $reply)
    {
        $reply->update($request->validated());

        return response()->json(new ReplyResource($reply), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reply $reply)
    {
        $reply->delete();

        return response()->json(null, 204);
    }
}
