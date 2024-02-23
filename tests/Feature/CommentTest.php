<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentTest extends TestCase
{
    public function test_user_can_get_comments()
    {
        Comment::factory(10)->create();

        $response = $this->get('/api/comments');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'links',
            'meta',
        ]);
    }

    public function test_user_can_get_specified_comment()
    {
        $comment = Comment::factory()->create();

        $response = $this->get("/api/comments/{$comment->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
        ]);
    }

    public function test_user_can_create_comment()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->post('/api/comments', [
            'content' => 'Test content',
            'post_id' => $post->id
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('comments', [
            'content' => 'Test content',
        ]);
    }

    public function test_user_can_update_comment()
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->put("/api/comments/{$comment->id}", [
            'content' => 'Updated test content',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('comments', [
            'content' => 'Updated test content',
        ]);
    }

    public function test_user_can_delete_comment()
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->delete("/api/comments/{$comment->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted('comments', [
            'id' => $comment->id,
        ]);
    }
}
