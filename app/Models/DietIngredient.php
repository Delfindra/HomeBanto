<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DietIngredient extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public function diet(): BelongsTo {
        return $this->belongsTo(Diet::class);
    }

    public function masterData(): BelongsTo {
        return $this->belongsTo(MasterData::class,'masterdata_id', 'id');
    }
}
