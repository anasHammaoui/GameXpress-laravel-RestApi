<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserManageController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
     if (auth('sanctum') -> user() -> can('view_users')){
        $users = User::all();
        return response()->json([
            'users' => $users
        ],200);
     }
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        if (auth('sanctum') -> user() -> can('view_users')){
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }
    
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
    
            return response()->json([
                'message' => 'User created successfully',
                'user' => $user
            ], 200);
        }
    }

    /**
     * Display the specified user.
     */
    public function show(string $id)
    {
       if (auth('sanctum') -> user() -> can('view_users')){
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'user' => $user
        ],200);
       }
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, string $id)
    {
       if(auth('sanctum') -> user() -> can('edit_users')){
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update([
            'name' => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User updated successfully',
            'user' => $user
        ]);
       }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(string $id)
    {
       if (auth('sanctum') -> user() -> can('delete_users')){
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ],200);
       }
    }
}
