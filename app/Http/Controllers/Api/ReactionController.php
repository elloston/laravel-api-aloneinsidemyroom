<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\ReplyResource;
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

        $reactable = $modelClass::findOrFail($id);

        $user_id = $request->user()->id;
        $reactionType = $request->type;

        $existingReaction = $reactable->reactions()->where('user_id', $user_id)->first();

        if ($existingReaction && $existingReaction->type === $reactionType) {
            $existingReaction->delete();
        } else {
            $reactable->reactions()->updateOrCreate(
                ['user_id' => $user_id],
                ['type' => $reactionType]
            );
        }

        $reactable->loadCount('likes', 'dislikes');

        $resourceMapping = $this->getResourceMapping();
        $resourceClass = $resourceMapping[$type] ?? null;

        if ($resourceClass) {
            return response()->json(new $resourceClass($reactable));
        }

        return response()->json(['message' => 'Resource class not found for the given type'], 500);
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

    protected function getResourceMapping()
    {
        return [
            'post' => PostResource::class,
            'comment' => CommentResource::class,
            'reply' => ReplyResource::class,
        ];
    }
}
