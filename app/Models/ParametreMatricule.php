<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParametreMatricule extends Model
{
    protected $fillable = [
        'prefix',
        'active',
    ];
}
