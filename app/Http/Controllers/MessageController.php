<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $messages = Message::with(['user', 'parent', 'replies', 'reactions'])->get();
        return MessageResource::collection($messages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MessageRequest $request)
    {
        $message = new Message($request->validated());
        $request->user()->messages()->save($message);

        return response($message, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message)
    {
        $message->load(['user', 'parent', 'replies', 'reactions']);
        return response()->json(new MessageResource($message), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MessageRequest $request, Message $message)
    {
        $message->update($request->validated());

        return response()->json($message, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        $message->delete();

        return response(null, 204);
    }

    public function trashed()
    {
        $messages = Message::onlyTrashed()->with(['user', 'parent', 'replies', 'reactions'])->get();
        return MessageResource::collection($messages);
    }

    public function showTrashed($id)
    {
        $message = Message::withTrashed()->findOrFail($id);

        $message->load(['user', 'parent', 'replies', 'reactions']);

        return response()->json(new MessageResource($message), 200);
    }
}
