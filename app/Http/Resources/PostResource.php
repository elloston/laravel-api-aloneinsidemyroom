<?php

namespace App\Http\Resources;

use App\Models\Post;
use App\Models\Reaction;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $user = Auth::guard('sanctum')->user();

        $currentUserReaction = null;
        if ($user) {
            $currentUserReaction = Reaction::where('reactable_type', Post::class)
                ->where('reactable_id', $this->id)
                ->where('user_id', $user->id)
                ->first();
        }

        return [
            'id' => $this->id,
            'content' => $this->content,

            'user' => new UserResource($this->whenLoaded('user')),
            'comments' => [
                'data' => CommentResource::collection($this->whenLoaded('comments')),
                'links' => [
                    'next' => $this->comments_count > 5 ? env('APP_URL') . "/api/posts/{$this->id}/comments?page=2" : null,
                ],
                'meta' => [
                    'total' => $this->comments_count ?? 0,
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
