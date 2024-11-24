<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterData extends Model
{
    protected $table = 'master_data';

    protected $fillable = [
        'image', 'name'
    ];

    protected $guarded = [
        'id'
    ];
}
