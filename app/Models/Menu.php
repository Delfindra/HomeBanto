<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    protected $table = 'recipes';

    protected $casts = [
        'ingredient' => 'array', // field ingredient JSON/LONGTEXT
    ];

    protected $guarded = [
        'id',
    ];

    // public function ingredients()
    // {
    //     return $this->belongsToMany(Ingredients::class, 'ingredient_recipe', 'recipe_id', 'ingredient_id');
    // }

}
