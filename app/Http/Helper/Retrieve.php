<?php

namespace App\Http\Helper;

use App\Models\Joblist;
use App\Models\Jobtype;

class Retrieve
{
    public static function customer($request)
    {
        $data = [
            'id'            => $request->customerid,
            'CustomerName'  => $request->customer,
            'Phone'         => $request->phone,
            'updated_at'    => now()
        ];

        return $data;
    }

    public static function vehicle($request)
    {
        $data = [
            'id'           => $request->vehicleid,
            'LisencePlate' => $request->nopol,
            'ModelType'    => $request->model,
            'updated_at'   => now()
        ];

        return $data;
    }

    public static function joblist($request)
    {
        $jobtype = Jobtype::where('name', $request->jobtype)->first();

        $data = [
            'id'             => $request->jobid,
            'ReleaseDate'    => $request->date,
            'JobTypeId'      => $jobtype->id,
            'updated_at'     => now()
        ];

        return $data;
    }
}
