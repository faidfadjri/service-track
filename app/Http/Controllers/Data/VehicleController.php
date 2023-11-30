<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\Joblist;
use App\Models\Role;
use App\Models\Vehicle;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class VehicleController extends Controller
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

    public function index()
    {
        return view('pages.data.vehicle', [
            'active' => 'Vehicle',
            'role' => $this->role
        ]);
    }

    public static function load(Request $request)
    {
        if ($request->ajax()) {
            $data = Vehicle::with('customer')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <div class="action-wrapper">
                            <button class="btn btn-info" data-id="' . $row->id . '">
                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i> 
                                Edit
                            </button>
                            <button class="btn btn-danger btn-delete-vehicle" data-id="' . $row->id . '">
                                <i class="fa fa-trash-o" aria-hidden="true"></i> 
                                Delete
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        } else {
            return response('Forbidden 403', 403);
        }
    }

    public function delete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), ['id' => 'required']);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $vehicle = Vehicle::find($request->id);

            if (!$vehicle) {
                return response()->json(['error' => 'Vehicle not found'], 404);
            }

            # Extract related IDs
            $vehicleIds = [$vehicle->id];
            $jobIds = Joblist::whereIn('VehicleId', $vehicleIds)->pluck('id')->toArray();

            # Delete related data
            History::whereIn('JobId', $jobIds)->delete();
            Joblist::destroy($jobIds);
            Vehicle::destroy($vehicleIds);

            return response()->json(['message' => 'Deleted Successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
