<?php

namespace App\Console\Commands;

use App\Models\Cart;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ClearExpiredCart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-expired-cart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove items from cart that are older than 48 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expirationTime = Carbon::now()->subHours(48);
        
        $deleted = Cart::where('created_at', '<', $expirationTime)->delete();
        if ($deleted > 0) 
        {
            $this->info("$deleted items removed from cart.");
        }
        else 
        {
            $this->info("No items to remove from cart.");
        }
    }
}

