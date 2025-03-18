<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class AdminController extends Controller
{
    // Permissions management
    public function assignPermissions(Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);
        $permissions = $request->permissions;

        $validatedPermissions  = Permission::whereIn('name', $permissions)->pluck('name')->toArray();

        if (count($validatedPermissions) !== count($permissions))
        {
            return response()->json(['error' => 'One or more permissions are invalid'], 400); // 400 = bad request
        }

        if (empty($validatedPermissions))
        {
            return response()->json(['error' => 'The permissions array cannot be empty'], 400); // 400 = bad request
        }

        // $user->syncPermissions($validated); 
        // sync for remove all permissions and assign new permissions
        // givePermissionTo for assign new permissions only
        
        $user->givePermissionTo($validatedPermissions);

        return response()->json(['message' => 'Permissions assigned successfully'], 200); // 200 = OK
    }

    // Roles management

    public function changeRole(User $user, Request $request){
        if (auth('sanctum') -> user() -> roles -> first() -> name === 'super_admin'){
            $validate = Validator::make($request->all(),[
                "role" => 'required|in:user_manager,product_manager,client',
            ]);
            if ($validate -> fails()){
                return response() -> json(["message" => "failed to change role"],422);
            }
            $user -> syncRoles([$request -> role]);
            return response() -> json(["message" => "Role changed with success", "role" => $user -> roles -> first() -> name ], 200);
        }
        return response() -> json(["message" => "Unauthorized"], 403);
    }
}
