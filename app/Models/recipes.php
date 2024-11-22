<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class recipes extends Model
{
    use HasFactory;

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
        return $this->belongsToMany(Ingredients::class, 'ingredient_recipe', 'recipe_id', 'ingredient_id');
    }
}
