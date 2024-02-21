<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testSignup()
    {
        $users = [
            [
                'username' => 'test',
                'email' => 'withusername@example.com',
                'password' => 'password',
                'password_confirmation' => 'password'
            ],
            [
                'email' => 'nousername@example.com',
                'password' => 'password',
                'password_confirmation' => 'password'
            ]
        ];

        foreach ($users as $user) {
            $response = $this->postJson('/api/signup', $user);

            $response->assertStatus(201);

            unset($user['password']);
            unset($user['password_confirmation']);

            $this->assertDatabaseHas('users', $user);
        }
    }

    public function testSignin()
    {
        User::factory()->create([
            'email' => 'signin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/signin', [
            'email' => 'signin@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
    }

    public function testSignout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/signout');

        $response->assertStatus(204);
    }
}
