<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Client;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use AuthorizesRequests;
    public function profile(){
        $data = new UserResource(Auth::user());
        return  response()->json([
            'message' => 'Your Profile ',
            'data' => $data,
        ],200);
    }

    public function dashboard(){
        // Dashboard for user
        // Dashboard for admin and super admin
    }

    public function index()
    {
        // For Admin
    }

    public function store(StoreUserRequest $request)
    {
        // Make Resource

    }

    public function showmyclient(User $user)
    {
        $user = Auth::user();
        $data = Client::where('created_by_user_id', $user->id)->get();
        return response()->json([
            'message' => 'Your Clients Retrieved Successfully',
            'data' => $data ,
        ],200);
    }


    public function updateProfile(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', User::class);
        $validated = $request->validated();
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }
        $user->update($validated);
        $data = new UserResource($user);
        return response()->json([
            'message' => 'User Profile Updated Successfully.',
            'data' => $data,
        ], 200);
    }

    public function destroy(User $user)
    {
        // Only Super Admin Can delete user
        $this->authorize('destroy', User::class);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully.'],200);
    }
}
