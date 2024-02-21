<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

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
}
