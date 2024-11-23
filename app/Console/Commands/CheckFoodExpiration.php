<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\ingredients;
use App\Notifications\FoodExpirationNotification;
use Carbon\Carbon;

class CheckFoodExpiration extends Command
{
    protected $signature = 'check:food-expiration';
    protected $description = 'Check for food items near expiration date';

    public function handle()
    {
        $currentDate = Carbon::now();
        
        // Ambil semua user
        $users = User::all();
    
        foreach ($users as $user) {
            // Ambil bahan yang akan kadaluarsa atau sudah kadaluarsa
            $foodsToNotify = Ingredients::where('users_id', $user->id)
                ->where(function ($query) use ($currentDate) {
                    $query->where('expiry_date', '<=', $currentDate->copy()->addDays(3)) // Dalam 3 hari ke depan
                          ->orWhere('expiry_date', '=', $currentDate); // Hari ini
                })
                ->get();
    
            if ($foodsToNotify->count() > 0) {
                foreach ($foodsToNotify as $food) {
                    // Update status menggunakan logika baru
                    $expiryDate = Carbon::parse($food->expiry_date);
    
                    if ($expiryDate->isToday()) {
                        // Jika tanggal kedaluwarsa adalah hari ini
                        $food->status = 'Expired (0 days left)';
                    } elseif ($expiryDate->lte($currentDate->addDays(3))) {
                        // Jika kedaluwarsa dalam 3 hari ke depan
                        $daysLeft = $expiryDate->diffInDays($currentDate, false);
                        $food->status = 'Nearly Expired (' . abs($daysLeft) . ' days left)';
                    } else {
                        // Jika kedaluwarsa lebih dari 3 hari ke depan
                        $daysLeft = $expiryDate->diffInDays($currentDate, false);
                        $food->status = 'Fresh (' . abs($daysLeft) . ' days left)';
                    }
    
                    $food->save(); 
    
                    $user->notify(new FoodExpirationNotification($food));
                }
    
                $this->info("Sent notifications for {$foodsToNotify->count()} items to {$user->name}");
            }
        }
    
        $this->info('Food expiration check completed');
    }    
}
