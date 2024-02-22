<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_user()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/user');

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'avatar' => $user->avatar,
        ]);
    }

    public function test_show_user()
    {
        $user = User::factory()->create([
            'username' => 'testuser',
        ]);

        $response = $this->getJson("/api/user/{$user->username}");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $user->id,
            'username' => 'testuser',
        ]);
    }
}
