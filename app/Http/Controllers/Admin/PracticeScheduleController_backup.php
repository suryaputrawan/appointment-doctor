<?php

namespace App\Http\Controllers\Admin;

use Throwable;
use Carbon\Carbon;
use App\Models\Doctor;
use Illuminate\Http\Request;
use App\Models\PracticeSchedule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class PracticeScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->type == 'datatable') {
            $data = PracticeSchedule::with([
                'doctor'    => function ($query) {
                    $query->select('id', 'name');
                }
            ])->orderBy('date')->get();

            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    $editRoute       = 'admin.practice-schedules.edit';
                    $deleteRoute     = 'admin.practice-schedules.destroy';
                    $viewRoute       = 'admin.practice-schedules.show';
                    $dataId          = Crypt::encryptString($data->id);
                    $dataDeleteLabel = Carbon::parse($data->start_time)->format('H:i') . '-'
                        . Carbon::parse($data->end_time)->format('H:i') . ' - '
                        . Carbon::parse($data->date)->format('d M Y') . ' - '
                        . $data->doctor->name;

                    $action = "";

                    $action .= '
                        <a class="btn btn-warning" id="btn-edit" type="button" data-url="' . route($editRoute, $dataId) . '">
                            <i class="fe fe-pencil"></i>
                        </a> ';

                    $action .= '
                        <button class="btn btn-danger delete-item" 
                            data-label="' . $dataDeleteLabel . '" data-url="' . route($deleteRoute, $dataId) . '">
                            <i class="fe fe-trash"></i>
                        </button> ';

                    $group = '<div class="btn-group btn-group-sm mb-1 mb-md-0" role="group">
                        ' . $action . '
                    </div>';
                    return $group;
                })
                ->editColumn('date', function ($data) {
                    return Carbon::parse($data->date)->format('d M Y');
                })
                ->addColumn('doctor_name', function ($data) {
                    return $data->doctor->name;
                })
                ->addColumn('time', function ($data) {
                    return Carbon::parse($data->start_time)->format('H:i')  . ' - ' . Carbon::parse($data->end_time)->format('H:i');
                })
                ->editColumn('booking_status', function ($data) {

                    $status = '<button class="btn btn-sm btn-rounded btn-primary">Available</button>';

                    if ($data->booking_status == 1) {
                        $status = '<button class="btn btn-sm btn-rounded btn-danger">Booked</button>';
                    }

                    return $status;
                })
                ->rawColumns(['action', 'date', 'doctor_name', 'time', 'booking_status'])
                ->make(true);
        }

        return view('admin.modules.practice-schedule.index', [
            'pageTitle'     => 'List Of Doctor Practice Schedule',
            'breadcrumb'    => 'Practice Schedules',
            'doctor'        => Doctor::orderBy('name', 'asc')->get(['id', 'name'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $dateNow = Carbon::now()->format('Y-m-d');
        $startTime = PracticeSchedule::query()
            ->where('date', $request->date)
            ->where('start_time', $request->start_time)->first();
        $endTime = PracticeSchedule::query()
            ->where('date', $request->date)
            ->where('end_time', $request->end_time)->first();

        if ($request->date != null && $request->date < $dateNow) {
            return response()->json([
                'status' => 404,
                'errorDate' => 'Date must be higher than current date...!',
            ]);
        }

        if ($startTime) {
            return response()->json([
                'status' => 404,
                'errorStartTime' => 'This time on ' . Carbon::parse($request->date)->format('d M Y') . ' already exists',
            ]);
        }

        if ($endTime) {
            return response()->json([
                'status' => 404,
                'errorEndTime' => 'This time on ' . Carbon::parse($request->date)->format('d M Y') . ' already exists',
            ]);
        }

        $validator = Validator::make([
            'date'                  => $request->date,
            'doctor'                => $request->doctor,
            'start_time'            => $request->start_time,
            'end_time'              => $request->end_time,
        ], [
            'date'                  => 'required',
            'doctor'                => 'required',
            'start_time'            => 'required',
            'end_time'              => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            DB::beginTransaction();

            try {
                PracticeSchedule::create([
                    'date'                  => $request->date,
                    'doctor_id'             => $request->doctor,
                    'start_time'            => $request->start_time,
                    'end_time'              => $request->end_time,
                ]);
                DB::commit();
                return response()->json([
                    'status'  => 200,
                    'message' => 'Schedule has been created',
                ], 200);
            } catch (Throwable $th) {
                DB::rollBack();
                return response()->json([
                    'status'  => 500,
                    'message' => $th->getMessage(),
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PracticeSchedule $practiceSchedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $id = Crypt::decryptString($id);
            $data = PracticeSchedule::find($id);

            if ($data) {
                return response()->json([
                    'status'    => 200,
                    'data'      => $data,
                ]);
            } else {
                return response()->json([
                    'status'    => 404,
                    'message'   => 'Schedule Not Found',
                ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 500,
                'message' => "Error on line {$e->getLine()}: {$e->getMessage()}",
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = PracticeSchedule::find($id);
        $dateNow = Carbon::now()->format('Y-m-d');

        if ($request->date != null && $request->date < $dateNow) {
            return response()->json([
                'status' => 400,
                'errors' => 'Date must be higher than current date...!',
            ]);
        }

        $validator = Validator::make([
            'date'                  => $request->date,
            'doctor'                => $request->doctor,
            'start_time'            => $request->start_time,
            'end_time'              => $request->end_time,
        ], [
            'date'                  => 'required',
            'doctor'                => 'required',
            'start_time'            => 'required',
            'end_time'              => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            if ($data) {
                DB::beginTransaction();

                try {
                    $data->update([
                        'date'                  => $request->date,
                        'doctor_id'             => $request->doctor,
                        'start_time'            => $request->start_time,
                        'end_time'              => $request->end_time,
                    ]);

                    DB::commit();

                    return response()->json([
                        'status'  => 200,
                        'message' => 'Schedule has been updated',
                    ], 200);
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return response()->json([
                        'status'  => 500,
                        'message' => $th->getMessage(),
                    ], 500);
                }
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Data not found..!',
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $id = Crypt::decryptString($id);
            $data = PracticeSchedule::find($id);

            if (!$data) {
                return response()->json([
                    'status'  => 404,
                    'message' => "Data not found!",
                ], 404);
            }

            $data->delete();

            DB::commit();

            return response()->json([
                'status'  => 200,
                'message' => "Schedule has been deleted..!",
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 500,
                'message' => "Error on line {$e->getLine()}: {$e->getMessage()}",
            ], 500);
        }
    }
}
