<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $request->validated();

        $request->authenticate();

        $user = Auth::user();

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->data([
                    'token' => $token,
                    'user' => $user,
        ], 'Login successful.');
    }
}
