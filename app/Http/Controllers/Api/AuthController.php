<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSocialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $validated = $request->validate([
            'username' => 'sometimes|nullable|string',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'name' => 'sometimes|nullable|string',
            'last_name' => 'sometimes|nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        $token = $user->createToken($request->header('User-Agent', 'unknown'))->plainTextToken;

        return response()->json(['token' => $token], 201);
    }

    public function signin(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Wrong email or password'],
            ]);
        }

        $token = $user->createToken($request->header('User-Agent', 'unknown'))->plainTextToken;

        return response()->json(['token' => $token], 200);
    }

    public function signout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response(null, 204);
    }

    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(Request $request, $provider)
    {
        $socialiteUser = Socialite::driver($provider)->user();

        $account = UserSocialAccount::where([
            'provider_name' => $provider,
            'provider_id' => $socialiteUser->getId(),
        ])->first();

        if (!$account) {
            $fakeUsername = $provider . '_user_' . $socialiteUser->getId();

            $user = User::firstOrCreate(
                [
                    'email' => $socialiteUser->getEmail() ?? $fakeUsername . '@example.com',
                    'username' => $socialiteUser->getNickname() ?? $fakeUsername,
                ],
                [
                    'name' => $socialiteUser->getName(),
                    'password' => Hash::make(Str::random(24)),
                    'avatar' => $socialiteUser->getAvatar(),
                ]
            );

            $account = UserSocialAccount::create(
                [
                    'provider_name' => $provider,
                    'provider_id' => $socialiteUser->getId(),
                    'token' => $socialiteUser->token,
                    'user_id' => $user->id
                ],
            );
        }

        $token = $account->user->createToken($request->header('User-Agent', 'unknown'))->plainTextToken;

        return redirect(env('FRONTEND_URL') . '/oauth/' .  '?token=' . $token);
    }
}
