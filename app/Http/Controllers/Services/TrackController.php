<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\History;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.public.track');
    }

    public function search(Request $request)
    {
        $data = $request->data;

        list($lisenceplate, $wo) = explode(':', $data);

        $conditions = [
            ['joblist.WO', $wo],
            ['vehicles.LisencePlate', $lisenceplate]
        ];

        $result = History::select(
            'customers.CustomerName',
            'vehicles.ModelType',
            'vehicles.LisencePlate',
            'joblist.WO',
            'joblist.ReleaseDate',
            'users.fullname',
            'master_roles.division as Division',
            'master_progress.name as Progress',
            'history.ProgressId',
            'history.ClockOnAt',
            'history.ClockOffAt',
            'history.created_at'
        )
            ->leftJoin('joblist', 'joblist.id', '=', 'history.JobId')
            ->leftJoin('vehicles', 'vehicles.id', '=', 'joblist.VehicleId')
            ->leftJoin('customers', 'customers.id', '=', 'vehicles.CustomerId')
            ->leftJoin('users', 'users.id', '=', 'joblist.UserId')
            ->leftJoin('master_roles', 'master_roles.id', '=', 'users.roleId')
            ->leftJoin('master_progress', 'master_progress.id', '=', 'history.ProgressId')
            ->where($conditions)
            ->orderByDesc('ProgressId')
            ->get();


        return $result;
    }
}
