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
use Illuminate\Support\Facades\Storage;

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

    public function dashboard()
    {
        $user = Auth::user();

        if (in_array($user->role, ['admin', 'super_admin'])) {
            return response()->json([
            'message' => 'Welcome to Admin Dashboard',
            'users_count' => User::count(),
            ]);
        }
        $data =Client::where('created_by_user_id', $user->id)->get();
        return response()->json([
            'message' => 'Welcome to User Dashboard',
            'clients_count' => $data->count(),
        ]);
    }

    public function index()
    {
        // For Super Admin and admin
         $this->authorize('manageUsers', User::class);

        return response()->json([
            'message' => 'All Users Retrieved Successfully',
            'data' => User::all(),
        ]);
    }

    public function create(StoreUserRequest $request)
    {
        // Make Resource
        // User can Register but not create super admin and admin can create user
        $this->authorize('create', User::class);
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $data =new UserResource($user);
        return response()->json([
            'message' => 'User created successfully.',
            'data' => $data,
        ], 201);
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
        if ($request->hasFile('image')) {
        // Delete old image
        if ($user->image && Storage::disk('public')->exists($user->image)) {
            Storage::disk('public')->delete($user->image);
        }

        // Upload new image
        $validated['image'] = $request->file('image')->store('users/images', 'public');
    }

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
