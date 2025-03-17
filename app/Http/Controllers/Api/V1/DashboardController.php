<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Notifications\StockNotifications;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;

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
