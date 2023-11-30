<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Helper\Collect;
use App\Http\Helper\Data;
use App\Http\Helper\Export;
use App\Http\Helper\Retrieve;
use App\Http\Helper\Store;
use App\Http\Helper\Update;
use App\Models\Customer;
use App\Models\History;
use App\Models\Joblist;
use App\Models\Jobtype;
use App\Models\Role;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRequest;
use App\Http\Requests\UpdateRequest;
use App\Models\Model;
use Illuminate\Support\Facades\Validator;
use Exception;


class AdminController extends Controller
{
    protected $role;
    protected $roleId;

    public function __construct()
    {
        $this->middleware('web');
        $this->middleware(function ($request, $next) {
            $this->roleId = $request->session()->get('user.roleId');
            $this->role = Role::where('id', $this->roleId)->pluck('name')->first();
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $jobtype  = Jobtype::pluck('name')->toArray();
        $model    = Model::pluck('name')->toArray();

        return view('pages.service.admin', [
            'active'  => 'Home',
            'jobtype' => $jobtype,
            'role'    => $this->role,
            'model'   => $model
        ]);
    }

    public function search(Request $request)
    {
        try {
            $nopol = $request->nopol;

            $vehicles = Vehicle::with('customer')
                ->where('LisencePlate', 'like', '%' . $nopol . '%')
                ->limit(10)
                ->get();

            return [
                'vehicles' => $vehicles
            ];
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function addJob(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'wo'        => 'required|unique:joblist,WO',
                'nopol'     => 'required',
                'date'      => 'required|date',
                'jobtype'   => 'required',
                'vehicleid' => 'required|exists:vehicles,id'
            ], [
                'wo.required'       => 'Nomor WO harus diisi.',
                'wo.unique'         => 'Nomor WO sudah digunakan.',
                'nopol.required'    => 'Nomor polisi harus diisi.',
                'date.required'     => 'Tanggal harus diisi.',
                'date.date'         => 'Format tanggal tidak valid.',
                'jobtype.required'  => 'Jenis pekerjaan harus diisi.',
                'vehicleid.required' => 'ID kendaraan harus diisi.',
                'vehicleid.exists'  => 'ID kendaraan tidak valid.',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $vehicleProgress = Joblist::where('VehicleId', $request->vehicleid)->orderByDesc('created_at')->first(['ProgressId', 'isCanceled']);
            $progress = $vehicleProgress->ProgressId;
            $cancel = $vehicleProgress->isCanceled;

            # Validate if the vehicle is in progress and not canceled
            if ($progress !== 14 && $cancel !== 1 && $vehicleProgress != null) {
                return response()->json(['error' => 'Status kendaraan belum selesai service'], 422);
            }

            // if (!in_array(14, $vehicleProgress) && $vehicleProgress != null) {
            //     return response()->json(['error' => 'Status kendaraan belum selesai service'], 422);
            // }

            # Collect Joblist & History data (when customer data already exist)
            $joblistData = Collect::newJoblist($request);
            $historyData = Collect::history($request, 1);

            # Save Record
            $joblist = Joblist::create($joblistData);
            $history = History::create($historyData);

            $messages = [
                "joblist"  => $joblist,
                "history"  => $history
            ];

            return $messages;
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(StoreRequest $request)
    {
        try {

            $wo = Joblist::pluck('WO');

            if ($wo->contains($request->wo)) {
                return response()->json(['error' => 'WO Already Exists'], 422);
            }

            # get all the data
            $customerData  = Collect::customer($request);
            $vehicleData   = Collect::vehicle($request);
            $joblistData   = Collect::joblist($request);
            $historyData1  = Collect::history($request, 1);
            $historyData2  = Collect::history($request, 2);

            # insert to database
            $customer  = Store::insert(Customer::class, $customerData);
            $vehicle   = Store::insert(Vehicle::class, $vehicleData);
            $joblist   = Store::insert(Joblist::class, $joblistData);

            # Insert 2 history data directly to make the status on "Waiting for Service"
            $history1  = Store::insert(History::class, $historyData1);
            $history2  = Store::insert(History::class, $historyData2);

            $messages = [
                "customer" => $customer,
                "vehicle"  => $vehicle,
                "joblist"  => $joblist,
                "history"  => $history2
            ];

            return $messages;
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(UpdateRequest $request)
    {
        try {

            $customer = Retrieve::customer($request);
            $vehicle  = Retrieve::vehicle($request);
            $joblist  = Retrieve::joblist($request);

            // return [
            //     "customer" => $customer,
            //     "vehicle" => $vehicle,
            //     "joblist" => $joblist
            // ];

            $updateCustomer = Update::data('Customer', $customer);
            $updateVehicle  = Update::data('Vehicle', $vehicle);
            $updateJoblist  = Update::data('Joblist', $joblist);

            return response()->json([
                "customer" => $updateCustomer,
                "vehicle"  => $updateVehicle,
                "joblist"  => $updateJoblist
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function excel()
    {
        $data = Data::masterData(1)->get();

        $file = Export::excel($data, 'data');

        return $file;
    }
}
