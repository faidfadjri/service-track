<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Helper\Data;
use App\Models\History;
use App\Models\Joblist;
use App\Models\Progress;
use App\Models\Role;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ServiceController extends Controller
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

    public function load(Request $request)
    {

        $role = $this->role;

        $load = Data::load($request, $role);

        return $load;
    }

    public function filter(Request $request)
    {
        $nopol = "";

        if ($request->has('nopol')) {
            $nopol = $request->nopol;
        }

        $role = $this->role;

        $data = Data::filter($nopol, $role)->get();

        return $data;
    }

    public function update(Request $request)
    {
        try {
            $role  = $this->role;
            $type  = $request->type;
            $date  = $request->Date;
            $jobId = $request->input('JobId');

            # Defining the progress ID for each role & type (start or end)
            $progress = Data::progress($type, $role);
            $notYetProgress = ($type === 'start') ? $progress : $progress - 2;

            # Validating the progress to be in the right place
            $notYet = Joblist::where('id', $jobId)
                ->where('ProgressId', $notYetProgress)
                ->whereDate('ServiceDate', $date)
                ->first();

            # Return an error message if the vehicle progress doesn't match the requirement
            if (!is_null($notYet)) {
                $status = Progress::find($notYetProgress)->value('name');
                $message = ($type === 'start') ? 'Status kendaraan sudah di ' : 'Status kendaraan masih di ';
                return response()->json(['error' => $message . $status], 422);
            }

            # Update progress on joblist table
            $joblist = Joblist::findOrFail($jobId);
            $joblist->update(['ProgressId' => $progress]);

            # Defining history data to be saved
            $historyData = [
                "JobId" => $jobId,
                "ProgressId" => $progress,
                "ClockOnAt" => now(),
                "ClockOffAt" => null
            ];

            # Customized history data based on the role & type
            if ($type === 'all' && $role !== "Cashier") {
                $historyData['ProgressId'] = $progress - 1;
                $historyData['ClockOffAt'] = now();
            } elseif ($type === 'finish') {
                $historyData['ProgressId'] = 14;
                $historyData['ClockOffAt'] = now();

                $joblist->update(['ServiceEndDate' => Carbon::now()]);
            }

            # update isPaid if the role is Cashier
            if ($role === "Cashier") {
                $joblist->update(['isPaid' => 1]);
            }

            if ($type === 'end') {
                $progressId = $progress - 1;
                History::where('JobId', $jobId)->where('ProgressId', $progressId)
                    ->whereDate('ClockOnAt', Carbon::today())
                    ->update(['ClockOffAt' => now()]);
            }

            History::create($historyData);

            # Insert 1 more history if there's only 1 button on the user's table
            if ($type === 'all' && $role !== 'Cashier') {
                $historyData['ProgressId'] = $progress;
                $historyData['ClockOffAt'] = null;
                History::create($historyData);
            }

            return response()->json(['message' => 'Data berhasil diperbarui.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Data tidak ditemukan.'], 404);
        } catch (QueryException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function pause(Request $request)
    {
        try {
            # Notes are required when the technician is about to paused the process
            $validator = Validator::make($request->all(), [
                'jobid' => 'required',
                'notes' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $jobid = $request->jobid;
            $notes = $request->notes;

            # Get history data for the specific JobId where progress in [Waiting for Service, Service, Paused]
            $history = History::where('JobId', $jobid)
                ->whereIn('ProgressId', [2, 3, 15])
                ->orderByDesc('ProgressId')
                ->first();

            # Specify the joblist data
            $joblist = Joblist::find($jobid);

            # Validate the data
            if (!$joblist || !$history) {
                return response()->json(['message' => 'Data not found'], 404);
            }

            # Defining pause data to update the history data (When The Technician Send a Pause Request)
            $pauseData = [
                'JobId'      => $jobid,
                'isPaused'   => 1,
                'PausedAt'   => now(),
                'Notes'      => $notes,
                'updated_at' => now()
            ];

            # When the technician continue the service progress
            $endPauseData = [
                'JobId'      => $jobid,
                'PausedOff'  => now(),
                'updated_at' => now()
            ];

            # Saved data to history and update the JobList table based on pause / continue condition
            if ($history->isPaused === 0 || $history->isPaused === null) { #--> Technician ask for pause
                $history->update($pauseData);
                $joblist->update(['ProgressId' => 15]);
            } elseif ($history->isPaused === 1) { #--> Technician continue the service
                $history->update($endPauseData);
                $joblist->update(['ProgressId' => 3]);
            }

            return response()->json(['message' => 'Data Berhasil di Update!'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), ['id' => 'required']);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            # Delete History and Joblist Data
            History::where('JobId', $request->id)->delete();
            Joblist::where('id', $request->id)->delete();

            return response()->json(['message' => 'Deleted Successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function cancel(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), ['id' => 'required']);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            # Update cancel flag
            Joblist::where('id', $request->id)->update(['isCanceled' => 1]);

            return response()->json(['message' => 'Canceled Successfully']);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
