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
        return $this->belongsToMany(
            Recipes::class,
            'recipe_ingredients_id',
            'recipe_id'
        );
    }

    // Update the status when fetching ingredients
    public static function boot()
    {
        parent::boot();
    }
}
