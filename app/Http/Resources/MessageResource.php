<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'user' => new UserResource($this->whenLoaded('user')),
            'parent' => $this->whenLoaded('parent'),
            'replies' => $this->whenLoaded('replies'),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
