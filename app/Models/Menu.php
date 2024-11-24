<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    protected $table = 'recipes';

    protected $fillable = [
        'name',
        'description',
        'instruction',
        'dificulty_level',
        'image',
        'cooking_time',
        'ingredient',     
        'created_at',
        'updated_at',
    ];

    protected $guarded = [
        'id',
    ];

    // public function ingredients()
    // {
    //     return $this->belongsToMany(Ingredients::class, 'ingredient_recipe', 'recipe_id', 'ingredient_id');
    // }

}
