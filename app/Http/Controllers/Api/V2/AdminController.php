<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class AdminController extends Controller
{
    // Permissions management

    public function assignPermissions(Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);
        $permissions = $request->permissions;

        $validated = Permission::whereIn('name', $permissions)->pluck('name')->toArray();

        if(empty($validated))
        {
            return response()->json(['error' => 'Invalid permissions'], 400); // 400 = bad request
        }

        // $user->syncPermissions($validated); 
        // sync for remove all permissions and assign new permissions
        // givePermissionTo for assign new permissions only

        $user->givePermissionTo($validated);

        return response()->json(['message' => 'Permissions assigned successfully'], 200); // 200 = OK
    }

    // Roles management


}
