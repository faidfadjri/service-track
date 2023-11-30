<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Progress extends Model
{
    use HasFactory;

    protected $table = 'master_progress';
    protected $fillable = [
        'JobId',
        'ProgressId',
        'created_at',
        'updated_at'
    ];

    public function joblists()
    {
        return $this->hasMany(Joblist::class, 'JobId', 'id');
    }
}
