<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Ingredients;

class recipes extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'instruction', 'dificulty_level', 'image', 'cooking_time', 'ingredient'
    ];

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'ingredient' => 'array', // field ingredient JSON/LONGTEXT
    ];
}
