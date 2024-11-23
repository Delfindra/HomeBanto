<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class recipe_ingredient extends Model
{
    protected $table = 'recipe_ingredients';

    public function recipe()
    {
        return $this->belongsTo(Recipes::class, 'recipe_id', 'recipe_id');
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredients::class, 'ingredient_id', 'ingredient_id');
    }
}
