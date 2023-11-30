<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Joblist extends Model
{
    use HasFactory;

    protected $table = 'joblist';
    protected $fillable = [
        'id',
        'VehicleId',
        'WO',
        'ServiceDate',
        'ServiceEndDate',
        'ReleaseDate',
        'isPaid',
        'isCanceled',
        'UserId',
        'ProgressId',
        'JobTypeId',
        'created_at',
        'updated_at'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'VehicleId', 'id');
    }

    public function progresses()
    {
        return $this->belongsTo(Progress::class, 'ProgressId', 'id');
    }
}
