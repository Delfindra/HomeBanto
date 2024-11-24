<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Ingredients extends Model
{
    protected $table = 'ingredients';

    protected $guarded = [
        'id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function recipes()
    {
        return $this->belongsToMany(
            Recipes::class,
            'recipe_ingredients_id',
            'recipe_id'
        );
    }

        // Update the status when fetching ingredients
    /**
     * Summary of boot
     * @return void
     */

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


    public function scopeIncomes($query)
    {
        return $query->where('type', 'income'); // Ganti 'type' sesuai kolom yang relevan
    }

    // Scope for expenses (optional)
    public function scopeExpenses($query)
    {
        return $query->where('type', 'expense'); // Ganti 'type' sesuai kolom yang relevan
    }


    public function scopeIncomes($query)
    {
        return $query->where('type', 'income'); // Ganti 'type' sesuai kolom yang relevan
    }

    // Scope for expenses (optional)
    public function scopeExpenses($query)
    {
        return $query->where('type', 'expense'); // Ganti 'type' sesuai kolom yang relevan
    }
}
