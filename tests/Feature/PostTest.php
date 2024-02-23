<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_posts()
    {
        Post::factory(10)->create();

        $response = $this->get('/api/posts');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'links',
            'meta',
        ]);
    }

    public function test_user_can_get_specified_post()
    {
        $post = Post::factory()->create();

        $response = $this->get("/api/posts/{$post->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
        ]);
    }

    public function test_user_can_create_post()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->post('/api/posts', [
            'content' => 'Test content',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('posts', [
            'content' => 'Test content',
        ]);
    }

    public function test_user_can_update_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->put("/api/posts/{$post->id}", [
            'content' => 'Updated test content',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('posts', [
            'content' => 'Updated test content',
        ]);
    }

    public function test_user_can_delete_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->delete("/api/posts/{$post->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted('posts', [
            'id' => $post->id,
        ]);
    }
}
