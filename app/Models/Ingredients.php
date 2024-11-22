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
        'expiry_date',
        'purchase_date',
        'quantity',
        'status',
        'category',
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
