<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $table    = 'history';
    protected $fillable = [
        'JobId',
        'ProgressId',
        'isPaused',
        'PausedAt',
        'PausedOff',
        'Notes',
        'ClockOnAt',
        'ClockOffAt',
        'created_at',
        'updated_at'
    ];
}
