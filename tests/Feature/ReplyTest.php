<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReplyTest extends TestCase
{
    public function test_user_can_get_replies()
    {
        Reply::factory(10)->create();

        $response = $this->get('/api/replies');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'links',
            'meta',
        ]);
    }

    public function test_user_can_get_specified_reply()
    {
        $reply = Reply::factory()->create();

        $response = $this->get("/api/replies/{$reply->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
        ]);
    }

    public function test_user_can_create_reply()
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->post('/api/replies', [
            'content' => 'Test content',
            'comment_id' => $comment->id
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('replies', [
            'content' => 'Test content',
        ]);
    }

    public function test_user_can_update_reply()
    {
        $user = User::factory()->create();
        $reply = Reply::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->put("/api/replies/{$reply->id}", [
            'content' => 'Updated test content',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('replies', [
            'content' => 'Updated test content',
        ]);
    }

    public function test_user_can_delete_reply()
    {
        $user = User::factory()->create();
        $reply = Reply::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->delete("/api/replies/{$reply->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted('replies', [
            'id' => $reply->id,
        ]);
    }
}
