<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    // Permissions management



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
