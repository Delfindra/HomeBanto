<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Diet extends Model
{
    use HasFactory;
    protected $guarded = [
        'id',
    ];

    public function dietIngredients(): HasMany {
        return $this->hasMany(DietIngredient::class);
    }
}
