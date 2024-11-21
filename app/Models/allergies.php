<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class allergies extends Model
{
    protected $table = 'allergies';

    protected $guarded = [
        'id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
