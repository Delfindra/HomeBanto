<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\recipes;
use App\Models\Ingredients;


class Menu extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    protected $guarded = [
        'id',
    ];

}
