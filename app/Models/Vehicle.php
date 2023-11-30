<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table    = 'vehicles';
    protected $fillable = [
        'id',
        'LisencePlate',
        'ModelType',
        'CustomerId',
        'created_at',
        'updated_at'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustomerId', 'id');
    }

    public function joblists()
    {
        return $this->hasMany(Joblist::class, 'VehicleId', 'id');
    }
}
