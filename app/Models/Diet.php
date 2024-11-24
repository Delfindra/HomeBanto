<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Diet extends Model
{
    protected $guarded = [
        'id',
    ];

    public function dietIngredients(): HasMany {
        return $this->hasMany(DietIngredient::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function recipes()
    {
        return $this->hasMany(recipes::class);
    }
    
}
