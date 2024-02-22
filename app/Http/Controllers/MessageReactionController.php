<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageReactionController extends Controller
{
    public function like(Request $request, Message $message)
    {
        $user_id = $request->user()->id;
        $existingReaction = $message->reactions()->where('user_id', $user_id)->first();

        if ($existingReaction && $existingReaction->type === 1) {
            $existingReaction->delete();
            return response()->json(['message' => 'Reaction removed'], 200);
        } else {
            $reaction = $message->reactions()->updateOrCreate(
                ['user_id' => $user_id],
                ['type' => 1]
            );
            return response()->json($reaction, 200);
        }
    }

    public function dislike(Request $request, Message $message)
    {
        $user_id = $request->user()->id;
        $existingReaction = $message->reactions()->where('user_id', $user_id)->first();

        if ($existingReaction && $existingReaction->type === -1) {
            $existingReaction->delete();
            return response()->json(['message' => 'Reaction removed'], 200);
        } else {
            $reaction = $message->reactions()->updateOrCreate(
                ['user_id' => $user_id],
                ['type' => -1]
            );
            return response()->json($reaction, 200);
        }
    }
}
