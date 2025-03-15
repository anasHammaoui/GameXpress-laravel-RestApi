<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\User;
use App\Notifications\EmptyStockNotification as NotificationsEmptyStockNotification;
use Illuminate\Console\Command;

class EmptyStockNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:empty-stock-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send to product managers email for stocks limit';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $productsManagers = User::with('roles') -> whereHas('roles',function($q){
            $q -> where('id',2);
        }) -> get();
        $outOfStock = $products = Product::where('stock',0) -> get();
        foreach($outOfStock as $product){
            $productOut = [
                "name" => $product -> name,
                "id" => $product -> id
            ];
            foreach($productsManagers as $manager){
                $manager -> notify(new NotificationsEmptyStockNotification($productOut));
            }

        }

    }
}
