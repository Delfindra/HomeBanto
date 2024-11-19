<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class allergies extends Model
{
    protected $table = 'allergies';

    protected $fillable = [
        'user_id',
        'allergy_name',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
