<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Reply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReactionTest extends TestCase
{
    use RefreshDatabase;

    protected function performReaction($user, $reactable, $reactableType, $reactionType, $second = false)
    {
        $response = $this->actingAs($user, 'sanctum')
            ->post("/api/perform_reaction_to/{$reactableType}/{$reactable->id}", ['type' => $reactionType]);

        if ($second == true) {
            $response->assertStatus(200);
            $this->assertDatabaseMissing('reactions', [
                'reactable_id' => $reactable->id,
                'user_id' => $user->id,
                'type' => $reactionType,
            ]);
        } else {
            $response->assertStatus(200);
            $this->assertDatabaseHas('reactions', [
                'reactable_id' => $reactable->id,
                'user_id' => $user->id,
                'type' => $reactionType,
            ]);
        }

        return $response;
    }

    /**
     * Post
     */
    public function test_user_can_react_to_post()
    {
        $user = User::factory()->create();
        $reactable = Post::factory()->create();
        $reactableType = 'post';

        $this->performReaction($user, $reactable, $reactableType, 1);
        $this->performReaction($user, $reactable, $reactableType, -1);
    }

    public function test_user_can_remove_react_from_post()
    {
        $user = User::factory()->create();
        $reactable = Post::factory()->create();
        $reactableType = 'post';

        $this->performReaction($user, $reactable, $reactableType, 1);
        $this->performReaction($user, $reactable, $reactableType, 1, true);
        $this->performReaction($user, $reactable, $reactableType, -1);
        $this->performReaction($user, $reactable, $reactableType, -1, true);
    }

    /**
     * Comment
     */
    public function test_user_can_react_comment()
    {
        $user = User::factory()->create();
        $reactable = Comment::factory()->create();
        $reactableType = 'comment';

        $this->performReaction($user, $reactable, $reactableType, 1);
        $this->performReaction($user, $reactable, $reactableType, -1);
    }

    public function test_user_can_remove_react_from_comment()
    {
        $user = User::factory()->create();
        $reactable = Comment::factory()->create();
        $reactableType = 'comment';

        $this->performReaction($user, $reactable, $reactableType, 1);
        $this->performReaction($user, $reactable, $reactableType, 1, true);
        $this->performReaction($user, $reactable, $reactableType, -1);
        $this->performReaction($user, $reactable, $reactableType, -1, true);
    }

    /**
     * Reply
     */
    public function test_user_can_react_reply()
    {
        $user = User::factory()->create();
        $reactable = Reply::factory()->create();
        $reactableType = 'reply';

        $this->performReaction($user, $reactable, $reactableType, 1);
        $this->performReaction($user, $reactable, $reactableType, -1);
    }

    public function test_user_can_remove_react_from_reply()
    {
        $user = User::factory()->create();
        $reactable = Reply::factory()->create();
        $reactableType = 'reply';

        $this->performReaction($user, $reactable, $reactableType, 1);
        $this->performReaction($user, $reactable, $reactableType, 1, true);
        $this->performReaction($user, $reactable, $reactableType, -1);
        $this->performReaction($user, $reactable, $reactableType, -1, true);
    }
}
