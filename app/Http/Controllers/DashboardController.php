<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
            if (auth('sanctum') -> user()-> hasRole("super_admin")){
                $countProducts = Product::count('id');
                $availableProducts = Product::where('status','available') -> count();
                $total_users = User::count('id');
                return [
                    "message" => "welcome to dashboard admin",
                    "product_count" => $countProducts,
                    "available_products" => $availableProducts,
                    "total_users" => $total_users
                ];
            }
            return ["message" => "you're not an admin"];
        }
    }
