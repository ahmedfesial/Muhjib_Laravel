<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;


class AdminController extends Controller
{
    use AuthorizesRequests;
    public function dashboard()
    {
        // Only admin and super admin should access this

        return response()->json([
            'message' => 'Welcome to the Admin Dashboard',
            'total_users' => User::count(),
        ]);
    }
    // Super Admin and admin create and manage users
    public function manageUsers()
    {
        $users = User::where('role', '!=', 'super-admin')->get();
     
        return response()->json([
            'message' => 'All Users Retrieved',
            'data' => $users,
        ]);
    }

    public function deleteUser(User $user){

        if ($user->role === 'super-admin') {
            return response()->json([
                'message' => 'Cannot delete super admin user',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
}
