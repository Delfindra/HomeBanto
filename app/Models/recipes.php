<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class recipes extends Model
{
    protected $table = 'recipes';

    protected $fillable = [
        'name',
        'description',
        'cooking_time',
        'image_url',
        'created_at',
        'updated_at',
    ];

    public function ingredients()
    {
        return $this->belongsToMany(
            ingredients::class,
            'recipe_ingredients_id',
            'recipe_id',
            'ingredient_id'
        );
    }
}
