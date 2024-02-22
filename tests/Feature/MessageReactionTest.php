<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MessageReactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_like_message()
    {
        $user = User::factory()->create();
        $message = Message::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->post("/api/messages/{$message->id}/like");

        $response->assertStatus(200);
        $this->assertDatabaseHas('message_reactions', [
            'message_id' => $message->id,
            'user_id' => $user->id,
            'type' => 1,
        ]);
    }

    public function test_user_can_remove_like()
    {
        $user = User::factory()->create();
        $message = Message::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->post("/api/messages/{$message->id}/like");
        $response->assertStatus(200);
        $this->assertDatabaseHas('message_reactions', [
            'message_id' => $message->id,
            'user_id' => $user->id,
            'type' => 1,
        ]);

        $response = $this->actingAs($user, 'sanctum')->post("/api/messages/{$message->id}/like");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('message_reactions', [
            'message_id' => $message->id,
            'user_id' => $user->id,
            'type' => 1,
        ]);
    }

    public function test_user_can_dislike_message()
    {
        $user = User::factory()->create();
        $message = Message::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->post("/api/messages/{$message->id}/dislike");

        $response->assertStatus(200);
        $this->assertDatabaseHas('message_reactions', [
            'message_id' => $message->id,
            'user_id' => $user->id,
            'type' => -1,
        ]);
    }

    public function test_user_can_remove_dislike()
    {
        $user = User::factory()->create();
        $message = Message::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->post("/api/messages/{$message->id}/dislike");
        $response->assertStatus(200);
        $this->assertDatabaseHas('message_reactions', [
            'message_id' => $message->id,
            'user_id' => $user->id,
            'type' => -1,
        ]);

        $response = $this->actingAs($user, 'sanctum')->post("/api/messages/{$message->id}/dislike");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('message_reactions', [
            'message_id' => $message->id,
            'user_id' => $user->id,
            'type' => -1,
        ]);
    }
}
