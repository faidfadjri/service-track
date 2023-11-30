<?php

namespace App\Http\Helper;

use App\Models\Joblist;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class Data
{
    public static function load($request, $userRole)
    {
        if ($request->ajax()) {

            $roleId = $request->session()->get('user.roleId');
            $role = Role::where('id', $roleId)->pluck('name');

            $data = self::masterData($userRole)->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($role) {
                    if ($role[0] === 'Admin' || $role[0] === 'Service Advisor') {
                        return '
                            <div class="action-wrapper">
                                <div class="dropdown">
                                    <a class="btn btn-info dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-gear-wide-connected"></i>
                                        Action
                                    </a>
                                
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item detail-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#detailModal"
                                                data-job-id="' . $row->id . '" 
                                                data-customer-id="' . $row->CustomerId . '" 
                                                data-vehicle-id="' . $row->VehicleId . '" 
                                                data-date="' . $row->Tanggal . '" 
                                                data-wo="' . $row->WO . '" 
                                                data-division="' . $row->Division . '" 
                                                data-progress="' . $row->Progress . '" 
                                                data-nopol="' . $row->Nopol . '" 
                                                data-model="' . $row->ModelType . '" 
                                                data-customer="' . $row->CustomerName . '" 
                                                data-jobtype="' . $row->JobType . '" 
                                                data-phone="' . $row->Phone . '" 
                                            >
                                                <i class="bi bi-pencil-fill"></i>
                                                Edit
                                            </a>
                                        </li>
                                        <li>
                                            <div class="action-wrapper">
                                                <a class="dropdown-item btn-notification"  
                                                    data-id="' . $row->id . '"
                                                    data-progress="' . $row->Progress . '" 
                                                    data-progressid="' . $row->ProgressId . '"
                                                    data-nopol="' . $row->Nopol . '"
                                                >
                                                    <i class="bi bi-bell-fill"></i>
                                                    Kirim Notifikasi
                                                </a>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="action-wrapper">
                                                <a class="dropdown-item btn-finish"
                                                    data-id="' . $row->id . '"
                                                    data-progress="' . $row->Progress . '"
                                                    data-nopol="' . $row->Nopol . '"
                                                >
                                                    <i class="bi bi-clipboard-check-fill"></i>
                                                    Finish
                                                </a>
                                            </div>
                                        </li>
                                        <div class="dropdown-divider"></div>
                                        <li>
                                            <div class="action-wrapper">
                                                <a class="dropdown-item btn-cancel-job color-red"
                                                    data-id="' . $row->id . '"
                                                >
                                                    <i class="bi bi-x-lg"></i>
                                                    Cancel
                                                </a>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="action-wrapper">
                                                <a class="dropdown-item btn-delete color-red"
                                                    data-id="' . $row->id . '"
                                                >
                                                    <i class="bi bi-trash"></i>
                                                    Hapus
                                                </a>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        ';
                    } else if ($role[0] === 'Teknisi') {
                        return '
                            <div class="action-wrapper">
                                <i class="datatable-action bi bi-play-fill btn-start-teknisi"
                                    id="start-' . $row->id . '"
                                    data-id="' . $row->id . '"
                                    data-progress="' . $row->Progress . '"
                                    data-nopol="' . $row->Nopol . '"
                                    data-date="' . $row->Tanggal . '"
                                ></i>                    
                                <i class="datatable-action bi bi-pause-fill btn-pause-teknisi" 
                                    id="pause"
                                    data-id="' . $row->id . '"
                                    data-nopol="' . $row->Nopol . '"
                                    data-progress="' . $row->Progress . '" 
                                ></i>
                                <i class="datatable-action bi bi-skip-forward-fill btn-end-teknisi" 
                                    data-id="' . $row->id . '" 
                                    data-progress="' . $row->Progress . '"
                                    data-nopol="' . $row->Nopol . '"
                                    data-date="' . $row->Tanggal . '"
                                    id="end"
                                ></i>
                            </div>
                        ';
                    } else if ($role[0] === 'Foreman') {
                        return '
                            <div class="action-wrapper">
                                <i class="datatable-action bi bi-play-fill btn-start-foreman"
                                    id="start-' . $row->id . '"
                                    data-id="' . $row->id . '"
                                    data-progress="' . $row->Progress . '"
                                    data-date="' . $row->Tanggal . '"
                                    data-nopol="' . $row->Nopol . '"
                                    data=
                                ></i>                    
                                <i class="datatable-action bi bi-skip-forward-fill btn-end-foreman" 
                                    data-id="' . $row->id . '"
                                    data-date="' . $row->Tanggal . '"
                                    data-progress="' . $row->Progress . '"
                                    data-nopol="' . $row->Nopol . '"
                                    id="end"
                                ></i>
                            </div>
                        ';
                    } else if ($role[0] === 'Washing') {
                        return '
                            <div class="action-wrapper">
                                <i class="datatable-action bi bi-play-fill btn-start-washing"
                                    id="start-' . $row->id . '"
                                    data-id="' . $row->id . '"
                                    data-date="' . $row->Tanggal . '"
                                    data-progress="' . $row->Progress . '"
                                    data-nopol="' . $row->Nopol . '"
                                ></i>                    
                                <i class="datatable-action bi bi-skip-forward-fill btn-end-washing" 
                                    data-id="' . $row->id . '" 
                                    data-date="' . $row->Tanggal . '"
                                    data-progress="' . $row->Progress . '"
                                    data-nopol="' . $row->Nopol . '"
                                    id="end"
                                ></i>
                            </div>
                        ';
                    } else if ($role[0] === 'Billing') {
                        return '
                            <div class="action-wrapper">
                                <i class="bi bi-cash-coin btn-end-billing action-btn"  
                                    data-id="' . $row->id . '"
                                    data-nopol="' . $row->Nopol . '"
                                ></i>
                            </div>
                        ';
                    } else if ($role[0] === 'Cashier') {
                        return '
                            <div class="action-wrapper">
                                <i class="bi bi-cash-coin btn-end-cashier action-btn"
                                    data-id="' . $row->id . '"
                                    data-nopol="' . $row->Nopol . '"
                                ></i>
                            </div>
                        ';
                    } else {
                        return '';
                    }
                })
                ->rawColumns(['action'])
                ->make(true);
        } else {
            return response('Forbidden 403', 403);
        }
    }

    public static function masterData($role)
    {
        $progressMap = [
            'Teknisi' => [2, 3, 15],
            'Foreman' => [4, 5],
            'Washing' => [6, 7],
            'Billing' => [8, 9],
            'Service Advisor' => range(1, 15),
            'Cashier'   => [12],
            'Dashboard' => array_merge(range(1, 13), [15]),
        ];

        $progress = $progressMap[$role] ?? [];

        $query = Joblist::select(
            'joblist.id',
            'joblist.VehicleId',
            'joblist.ReleaseDate',
            'vehicles.id as CustomerId',
            DB::raw('joblist.ServiceDate as Tanggal'),
            'joblist.WO',
            'master_roles.division as Division',
            DB::raw('CASE WHEN isCanceled = 1 THEN "Cancel" ELSE master_progress.name END as Progress'),
            'master_progress.id as ProgressId',
            'vehicles.LisencePlate as Nopol',
            'vehicles.ModelType as ModelType',
            'customers.CustomerName',
            'master_job.name as JobType',
            'customers.Phone'
        )
            ->leftJoin('vehicles', 'vehicles.id', '=', 'joblist.VehicleId')
            ->leftJoin('customers', 'customers.id', '=', 'vehicles.CustomerId')
            ->leftJoin('users', 'users.id', '=', 'joblist.UserId')
            ->leftJoin('master_roles', 'master_roles.id', '=', 'users.roleId')
            ->leftJoin('master_progress', 'master_progress.id', '=', 'joblist.ProgressId')
            ->leftJoin('master_job', 'master_job.id', '=', 'joblist.JobTypeId')
            ->whereIn('joblist.ProgressId', $progress)
            ->orderByDesc('joblist.ServiceDate');


        // if ($request->has('startdate') && $request->has('enddate')) {
        //     // $query->whereBetween('joblist.ServiceDate', [$request->input('startdate'), $request->input('enddate')]);
        //     return $query->get();
        // }

        return $query;
    }


    public static function progress($type, $role)
    {
        $progressMap = [
            'start' => [
                'Teknisi' => 3,
                'Foreman' => 5,
                'Washing' => 7,
            ],
            'end' => [
                'Teknisi' => 4,
                'Foreman' => 6,
                'Washing' => 8,
            ],
            'all' => [
                'Billing' => 10,
                'Service Advisor' => 12,
                'Cashier' => 13,
            ],
            'finish' => [
                'Service Advisor' => 14
            ]
        ];

        return $progressMap[$type][$role] ?? 0;
    }

    public static function filter($nopol, $role)
    {
        $progressMap = [
            'Teknisi' => [2, 3, 15],
            'Foreman' => [4, 5],
            'Washing' => [6, 7],
            'Billing' => [8, 9],
            'Service Advisor' => range(1, 14, 1),
            'Cashier' => [12, 13],
            'Dashboard' => range(1, 13, 1) + [16],
        ];

        $progress = $progressMap[$role] ?? [];

        return Joblist::select(
            'joblist.id',
            DB::raw('DATE(joblist.ServiceDate) as Tanggal'),
            'joblist.WO',
            'master_roles.division as Division',
            'master_progress.name as Progress',
            'joblist.ProgressId as ProgressId',
            'vehicles.LisencePlate as Nopol',
            'vehicles.ModelType as ModelType',
            'customers.CustomerName',
            'master_job.name as JobType',
            'customers.Phone'
        )
            ->leftJoin('vehicles', 'vehicles.id', '=', 'joblist.VehicleId')
            ->leftJoin('customers', 'customers.id', '=', 'vehicles.CustomerId')
            ->leftJoin('users', 'users.id', '=', 'joblist.UserId')
            ->leftJoin('master_roles', 'master_roles.id', '=', 'users.roleId')
            ->leftJoin('master_progress', 'master_progress.id', '=', 'joblist.ProgressId')
            ->leftJoin('master_job', 'master_job.id', '=', 'joblist.JobTypeId')
            ->where('vehicles.LisencePlate', 'like', "%$nopol%")
            ->whereIn('joblist.ProgressId', $progress)
            ->orderByDesc('id');
    }
}
