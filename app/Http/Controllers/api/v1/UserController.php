<?php

namespace App\Http\Controllers\api\v1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // View all users
        $users = User::all();

        return response()->json($users);
    }

    public function getUserById($id)
    {
        // Assuming you have a User model and you're using Eloquent
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    public function getAllUsers()
    {
        $users = User::all();

        return response()->json($users);
    }

    public function getUserByEmail($email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    public function activateUser($id)
    {
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $user->is_active = true;
    $user->save();

    return response()->json(['message' => 'User account activated']);
    }
    
    public function deactivateUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->is_active = false;
        $user->save();

        return response()->json(['message' => 'User account deactivated']);
    }
    
    public function getUserByRole($role)
    {
        $users = User::where('role', $role)->get();

        return response()->json($users);
    }
    public function getUserByStatus($status)
    {
        $users = User::where('is_active', $status)->get();

        return response()->json($users);
    }
}
