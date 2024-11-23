<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Ingredients extends Model
{
    protected $table = 'ingredients';

    protected $fillable = [
        'users_id',
        'name',
        'quantity',
        'status',
        'category',
        'purchase_date',
        'expiry_date',  
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }


    public function recipes()
    {
        return $this->belongsToMany(recipes::class, 'ingredient_recipe', 'ingredient_id', 'recipe_id');
    }

    // Update the status when fetching ingredients
    public static function boot()
    {
        parent::boot();

        // Listen for the saving event
        static::saving(function ($ingredient) {
            // Parse expiry_date and get current date
            $expiryDate = Carbon::parse($ingredient->expiry_date);
            $currentDate = Carbon::now();

            // Calculate days left until expiry
            $daysLeft = $currentDate->diffInDays($expiryDate, false);

            $daysLeft = (int) $daysLeft;

            // Update the status based on the expiry date
            if ($daysLeft < 1) {
                // Expired if daysLeft is negative
                $ingredient->status = 'Expired (' . abs($daysLeft) . ' days ago)';
            } elseif ($daysLeft <= 3) {
                // Nearly expired if 0 to 7 days left
                $ingredient->status = 'Nearly Expired (' . $daysLeft . ' days left)';
            } else {
                // Fresh if more than 7 days left
                $ingredient->status = 'Fresh (' . $daysLeft . ' days left)';
            }
        });
    }

    // Update the status when fetching ingredients
    public static function boot()
    {
        parent::boot();

        // Listen for saving event
        static::saving(function ($ingredient) {
            $expiryDate = Carbon::parse($ingredient->expiry_date);
            $currentDate = Carbon::now();

            $daysLeft = $expiryDate->diffInDays($currentDate, false);

            // Update the status based on the expiry date
            if ($expiryDate->lt($currentDate)) {
                $ingredient->status = 'Expired (' . abs(intval($daysLeft)) . ' days ago)';
            } elseif ($expiryDate->lte($currentDate->addDays(7))) {
                $ingredient->status = 'Nearly Expired (' . abs(intval($daysLeft)) . ' days left)';
            } else {
                $ingredient->status = 'Fresh (' . abs(intval($daysLeft)) . ' days left)';
            }
        });
    }
}
