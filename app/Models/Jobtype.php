<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jobtype extends Model
{
    protected $table = 'master_job';
    protected $fillable= [
        'id',
        'name',
        'created_at',
        'updated_at'
    ];
}
