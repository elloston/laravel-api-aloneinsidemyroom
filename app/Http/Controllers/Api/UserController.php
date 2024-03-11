<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Get current user
     *
     * @param Request $request
     * @return Response
     */
    public function current(Request $request)
    {
        return response($request->user());
    }

    /**
     * Get user by username.
     *
     * @param string $username
     * @return Response
     */
    public function show(string $username)
    {
        $user = User::where('username', $username)->firstOrFail();

        return response($user);
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|file',
        ]);

        $file = $request->file('avatar');
        $newAwatarPath = $file->store('public/avatars');

        $user = $request->user();

        // Check exist avatar
        if ($user->avatar && Storage::exists($user->avatar)) {
            Storage::delete($user->avatar);
        }

        // Save new avatar
        $user->avatar = $newAwatarPath;
        $user->save();

        return response()->json($user, 200);
    }
}
