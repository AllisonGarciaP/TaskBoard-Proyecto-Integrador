<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $datos = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'dispositivo' => ['required', 'string', 'max:100'],
        ]);

        $user = User::where('email', $datos['email'])->first();

        if (! $user || ! Hash::check($datos['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales no coinciden.'],
            ]);
        }

        $token = $user->createToken($datos['dispositivo'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'tipo' => 'Bearer',
            'usuario' => $user->only('id', 'name', 'email'),
        ], 201);
    }

    public function yo(Request $request)
    {
        return response()->json($request->user()->only('id', 'name', 'email'));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }

    public function tokens(Request $request)
    {
        $tokens = $request->user()->tokens()
            ->orderBy('id')
            ->get(['id', 'name', 'last_used_at', 'created_at']);

        return response()->json($tokens);
    }

    public function destroyToken(Request $request, string $tokenId)
    {
        $request->user()->tokens()->findOrFail($tokenId)->delete();

        return response()->noContent();
    }
}
