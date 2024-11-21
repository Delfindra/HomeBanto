<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'intruction', 'cooking_time', 'diffcutly_level', 'image',
    ];

    protected $guarded = [
        'id',
    ];

    public function ingredients()
    {
        return $this->hasMany(Ingredients::class);
    }
}
