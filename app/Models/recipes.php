<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Ingredients;

class recipes extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'instruction', 'dificulty_level', 'image', 'cooking_time',
    ];

    protected $guarded = [
        'id',
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
