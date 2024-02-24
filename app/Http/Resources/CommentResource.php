<?php

namespace App\Http\Resources;

use App\Models\Comment;
use App\Models\Reaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Current user reaction
        $user = Auth::guard('sanctum')->user();

        $currentUserReaction = null;
        if ($user) {
            $currentUserReaction = Reaction::where('reactable_type', Comment::class)
                ->where('reactable_id', $this->id)
                ->where('user_id', $user->id)
                ->first();
        }

        return [
            'id' => $this->id,
            'content' => $this->content,

            'user' => new UserResource($this->whenLoaded('user')),
            // replies
            'replies' => [
                'data' => [],
                'links' => [
                    'next' => $this->replies_count > 0 ? env('APP_URL') . "/api/comments/{$this->id}/replies?page=1" : null,
                ],
                'meta' => [
                    'total' => $this->replies_count ?? 0,
                ]
            ],

            'likes_count' => $this->likes_count ?? 0,
            'dislikes_count' => $this->dislikes_count ?? 0,
            'current_user_reaction' => $currentUserReaction,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
