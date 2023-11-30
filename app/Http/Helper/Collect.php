<?php

namespace App\Http\Helper;

use App\Models\Customer;
use App\Models\Joblist;
use App\Models\Jobtype;
use App\Models\Vehicle;

class Collect
{
    private static function generateCustomerId()
    {
        $latestId = Customer::max('id');
        return $latestId + 1;
    }

    private static function generateVehicleId()
    {
        $latestId = Vehicle::max('id');
        return $latestId + 1;
    }

    private static function generateJoblistId()
    {
        $latestId = Joblist::max('id');
        return $latestId + 1;
    }

    public static function customer($request)
    {
        $customerId = self::generateCustomerId();

        $data = [
            'id'            => $customerId,
            'CustomerName'  => $request->customer,
            'Phone'         => $request->phone,
            'created_at'    => now(),
            'updated_at'    => now()
        ];

        return $data;
    }

    public static function vehicle($request)
    {
        $vehicleId = self::generateVehicleId();

        $data = [
            'id' => $vehicleId,
            'LisencePlate' => $request->nopol,
            'ModelType'    => $request->model,
            'CustomerId'   => self::generateCustomerId(),
            'created_at'   => now(),
            'updated_at'   => now()
        ];

        return $data;
    }

    public static function joblist($request)
    {
        $jobId = self::generateJoblistId();
        $user = $request->session()->get('user');
        $jobtype = Jobtype::where('name', $request->jobtype)->first();

        $wo = Joblist::pluck('WO');

        if ($wo->contains($request->wo)) {
            return response()->json(['error' => 'WO Already Exists'], 422);
        }

        $data = [
            'id'             => $jobId,
            'VehicleId'      => self::generateVehicleId(),
            'WO'             => $request->wo,
            'ServiceDate'    => now(),
            'ServiceEndDate' => null,
            'ReleaseDate'    => $request->date,
            'isPaid'         => 0,
            'UserId'         => $user->id,
            'ProgressId'     => 2,
            'JobTypeId'      => $jobtype->id,
            'created_at'     => now(),
            'updated_at'     => now()
        ];

        return $data;
    }

    public static function history($request, $progress)
    {
        $jobId = self::generateJoblistId();

        $data = [
            'JobId'      => $jobId,
            'ProgressId' => $progress,
            'ClockOnAt'  => now(),
            'created_at' => now(),
            'updated_at' => now()
        ];

        return $data;
    }

    public static function newJoblist($request)
    {
        $wo      = $request->wo;
        $nopol   = $request->nopol;
        $date    = $request->date;
        $jobtype = $request->jobtype;

        $vehicle = Vehicle::with('customer')->where('LisencePlate', $nopol)->first();
        $jobtype = Jobtype::where('name', $request->jobtype)->first();
        $user    = $request->session()->get('user');

        $latestId = Joblist::max('id');

        $joblistData = [
            "id"             => $latestId + 1,
            "VehicleId"      => $vehicle->id,
            "WO"             => $wo,
            'ServiceDate'    => now(),
            'ServiceEndDate' => null,
            'ReleaseDate'    => $date,
            'isPaid'         => 0,
            'UserId'         => $user->id,
            'ProgressId'     => 1,
            'JobTypeId'      => $jobtype->id,
            'created_at'     => now(),
            'updated_at'     => now()
        ];

        return $joblistData;
    }
}
