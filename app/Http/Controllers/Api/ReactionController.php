<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Reply;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    public function performReaction(Request $request, $type, $id)
    {
        $modelClass = $this->getModelClass($type);

        if (!$modelClass) {
            return response()->json(['message' => 'Invalid reactable type'], 400);
        }

        $reactable = $modelClass::find($id);

        if (!$reactable) {
            return response()->json(['message' => 'Reactable not found'], 404);
        }

        $user_id = $request->user()->id;
        $reactionType = $request->type;

        $existingReaction = $reactable->reactions()->where('user_id', $user_id)->first();

        if ($existingReaction && $existingReaction->type === $reactionType) {
            $existingReaction->delete();
            return response()->json(['message' => 'Reaction removed'], 200);
        } else {
            $reaction = $reactable->reactions()->updateOrCreate(
                ['user_id' => $user_id],
                ['type' => $reactionType]
            );
            return response()->json($reaction, 200);
        }
    }

    protected function getModelClass($type)
    {
        $types = [
            'post' => Post::class,
            'comment' => Comment::class,
            'reply' => Reply::class,
        ];

        return $types[$type] ?? null;
    }
}
