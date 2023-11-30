<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table    = 'master_roles';
    protected $fillable = [
        'name',
        'division',
        'created_at',
        'updated_at'
    ];
}
