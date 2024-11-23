<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'ingredient' => 'array', // field ingredient JSON/LONGTEXT
    ];

    // public function ingredients()
    // {
    //     return $this->belongsToMany(Ingredients::class, 'ingredient_recipe', 'recipe_id', 'ingredient_id');
    // }

}
