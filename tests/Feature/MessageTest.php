<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_message()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->post('/api/messages', [
            'content' => 'Test message',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('messages', [
            'content' => 'Test message',
        ]);
    }

    public function test_user_can_get_messages()
    {
        Message::factory(10)->create();

        $response = $this->get('/api/messages');

        $response->assertStatus(200);
    }

    public function test_user_can_get_specified_message()
    {
        $message = Message::factory()->create();

        $response = $this->get("/api/messages/{$message->id}");

        $response->assertStatus(200);
    }

    public function test_user_can_update_message()
    {
        $user = User::factory()->create();
        $message = Message::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->put("/api/messages/{$message->id}", [
            'content' => 'Updated test message',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('messages', [
            'content' => 'Updated test message',
        ]);
    }

    public function test_user_can_delete_message()
    {
        $user = User::factory()->create();
        $message = Message::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->delete("/api/messages/{$message->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted('messages', [
            'id' => $message->id,
        ]);
    }
}
