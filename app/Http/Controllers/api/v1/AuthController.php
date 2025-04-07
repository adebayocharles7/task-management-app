<?php

namespace App\Http\Controllers\api\v1;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function register(Request $request) {
        // Validate the incoming request
    $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed', // Password confirmation required
        'role' => 'nullable|string|in:user,admin', // Optional role field, must be 'user' or 'admin'
       
        // go check the relationships in ths models after
    ]);

    // Create the user
    $user = User::create([
        'first_name' => $validated['first_name'],
        'last_name' => $validated['last_name'],
        'email' => $validated['email'],
        'password' =>bcrypt ($validated['password']), // Hash the password
        'role' => $validated['role'] ?? 'user', // get the role from the request or just use the default role: 'user'
        'is_active' => true, // Default account status is active
    ]);
    
     // Generate JWT token
     $token = JWTAuth::fromUser($user);
     
    return response()->json([
        'message' => 'User registered successfully',
        'user' => $user,
        'token' => $token,
    ], 201);
}

    public function login (Request $request) {
        // assign the request to a variable
        $credentials = $request->only('email', 'password');

        // Attempt login with the provided credentials
        $token = JWTAuth::attempt($credentials);
        if (!$token) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
        // Set the token in the auth guard
        auth('api')->setToken($token); // This binds the token to the 'api' guard 

        return response()->json([
            'message' => 'User logged in successfully',
            'user' => auth('api')->user(),
            'token' => $token,
        ]);
    }

    public function logout (Request $request) {
        
        try{
            // Get the token from the request manually
            $token = JWTAuth::getToken();

        if (!$token) {
            return response()->json(['error' => 'No token provided'], 400);
        }
    
        // Invalidate the token
        JWTAuth::invalidate($token);

        return response()->json(['message' => 'User logged out successfully']);

        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Failed to logout, token might be invalid or expired'
            ], 500);
        } 
    
    }
}
