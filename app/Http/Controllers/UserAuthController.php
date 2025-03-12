<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserAuthController extends Controller
{
    // :register function 
    public function register(Request $request){
        // validate data
        $data = Validator::make($request -> all(),[
            'name' => 'required|string',
            'email' => 'required|email|string|unique:users',
            'password' => 'required|min:8',
        ]);
        // :check if data is correct 
        if ($data -> fails()){
            return response() -> json([
                "errors" => $data -> errors()
            ],422);
          }  else {
          
            if (User::count() === 0){
                // if it's correct register the user
                $user =  User::create([
                'name' => $request-> name,
                'email' => $request -> email,
                'password' => Hash::make($request-> password),
            ]);
                $user -> assignRole('super_admin');
                return response() -> json([
                    "message" => 'Account created successfully',
                    "role" => $user -> roles -> first() -> name,
                ],200);
            } 
            //  register others user if they're not admin without role till admin give it to them
              $user =  User::create([
                'name' => $request-> name,
                'email' => $request -> email,
                'password' => Hash::make($request-> password),
            ]);
            $user -> assignRole('product_manager');
                return response() -> json([
                    "message" => 'Account created successfully',
                    "role" => "Pending admin",
                ],200);
           
          }
    }
    // login funciton
    public function login(Request $request){
        $data = Validator::make($request -> all(),[
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);
    
        if ($data -> fails()){
            return response() -> json([
                "errors" => $data -> errors()
            ],422);
        }
        $user = User::where('email',$request -> email) -> first();
        
        if (!$user || !Hash::check($request -> password, $user -> password)){
            return response() -> json([
                "message" => "Email or password is incorrect"
            ],401);
        }
      
        $token = $user -> createToken($request -> email) -> plainTextToken;
        return response() -> json([
            "message" => "You loged in successfully",
            "role" => $user -> roles -> first() ->name,
            "permission" => $user -> getAllPermissions(),
            "access_token" => $token
        ]);
    }
    // logout funciton
    public function logout(){
        if (auth('sanctum') -> check()){
            auth() -> user()-> tokens() -> delete();
            return response() -> json([
                "message" => "Logged out successfully"
            ]);
        }
    }
}
