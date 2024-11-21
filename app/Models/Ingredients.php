<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredients extends Model
{
    protected $table = 'ingredients';

    protected $fillable = [
        'users_id',
        'name',
        'expiry_date',
        'purchase_date',
        'quantity',
        'created_at',
        'updated_at',
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
}
