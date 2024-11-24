<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class recipes extends Model
{
    protected $table = 'recipes';

    protected $fillable = [
        'name', 'description', 'instruction', 'dificulty_level', 'image', 'cooking_time', 'ingredient',
    ];


    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'ingredient' => 'array', // field ingredient JSON/LONGTEXT
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
