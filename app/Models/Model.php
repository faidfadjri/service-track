<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model AS EloquentModel;

class Model extends EloquentModel
{
    protected $table = 'master_model';
    protected $fillable = [
        'id',
        'name',
        'created_at',
        'updated_at'
    ];
}
