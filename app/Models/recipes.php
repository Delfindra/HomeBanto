<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use App\Models\Ingredients;

class recipes extends Model
{
    use HasFactory;

    public function menu()
    {
        return $this->belongsTo(Menu::class); // Assuming 'menu_id' is the foreign key in the 'recipes' table
    }

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
