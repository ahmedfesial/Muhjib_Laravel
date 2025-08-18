<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
        'phone' => ['required', 'string', 'max:20'],
        'role' => ['required','string','in:user,admin,super_admin'],
        'image' => ['nullable', 'image', 'max:255'],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    DB::beginTransaction();

    try {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $request->role,
            'image' => $request->image ? asset('storage/' . $request->image) : null,
        ]);

        // Generate token
        $token = JWTAuth::fromUser($user);

        // Commit transaction
        DB::commit();

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Registration failed',
            'error' => $e->getMessage(),
        ], 500);
    }
}
}
