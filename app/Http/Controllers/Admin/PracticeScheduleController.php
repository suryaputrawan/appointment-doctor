<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Throwable;
use Carbon\Carbon;
use App\Models\Doctor;
use App\Models\Hospital;
use Illuminate\Http\Request;
use App\Models\PracticeSchedule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\PracticeScheduleRequest;

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
                    $query->select('id', 'name', 'isAktif');
                },
                'hospital'  => function ($query) {
                    $query->select('id', 'name');
                }
            ])
                ->orderBy('date', 'desc')
                ->orderBy('doctor_id', 'asc')
                ->orderBy('start_time', 'asc')->get();

            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    $user            = auth()->user();
                    $editRoute       = 'admin.practice-schedules.edit';
                    $deleteRoute     = 'admin.practice-schedules.destroy';
                    $viewRoute       = 'admin.practice-schedules.show';
                    $dataId          = Crypt::encryptString($data->id);
                    $dataDeleteLabel = Carbon::parse($data->start_time)->format('H:i') . '-'
                        . Carbon::parse($data->end_time)->format('H:i') . ' - '
                        . Carbon::parse($data->date)->format('d M Y') . ' - '
                        . $data->doctor->name;

                    $action = "";

                    if ($user->can('update doctor schedules')) {
                        $action .= '
                            <a class="btn btn-warning" id="btn-edit" type="button" data-url="' . route($editRoute, $dataId) . '">
                                <i class="fe fe-pencil"></i>
                            </a> ';
                    }

                    if ($user->can('delete doctor schedules')) {
                        $action .= '
                            <button class="btn btn-danger delete-item" 
                                data-label="' . $dataDeleteLabel . '" data-url="' . route($deleteRoute, $dataId) . '">
                                <i class="fe fe-trash"></i>
                            </button> ';
                    }

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
                ->addColumn('hospital', function ($data) {
                    return $data->hospital->name;
                })
                ->rawColumns(['action', 'date', 'doctor_name', 'time', 'booking_status', 'hospital'])
                ->make(true);
        }

        return view('admin.modules.practice-schedule.index', [
            'pageTitle'     => 'List Of Doctor Practice Schedule',
            'breadcrumb'    => 'Practice Schedules',
            'doctor'        => Doctor::where('isAktif', 1)->orderBy('name', 'asc')->get(['id', 'name']),
            'hospital'      => Hospital::orderBy('name', 'asc')->get(['id', 'name'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        if ($user->can('create doctor schedules')) {
            return view('admin.modules.practice-schedule.create', [
                'pageTitle'     => 'Create Practice Schedule',
                'breadcrumb'    => 'Create Practice Schedule',
                'btnSubmit'     => 'Save',
                'doctor'        => Doctor::where('isAktif', 1)->orderBy('name', 'asc')->get(['id', 'name']),
                'hospital'      => Hospital::orderBy('name', 'asc')->get(['id', 'name'])
            ]);
        } else {
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PracticeScheduleRequest $request)
    {
        $user = auth()->user();

        if ($user->can('create doctor schedules')) {
            try {
                DB::beginTransaction();

                //Store multiple data
                if ($request->date && $request->start_time && $request->end_time) {
                    for ($i = 0; $i < count($request->date); $i++) {
                        if ($request->date[$i]) {
                            PracticeSchedule::firstOrCreate([
                                'doctor_id'             => $request->doctor,
                                'hospital_id'           => $request->hospital,
                                'date'                  => $request->date[$i],
                                'start_time'            => $request->start_time[$i],
                                'end_time'              => $request->end_time[$i],
                            ]);
                        }
                    }
                }

                DB::commit();

                if (isset($_POST['btnSimpan'])) {
                    return redirect()->route('admin.practice-schedules.index')
                        ->with('success', 'Practice Schedules has been created');
                } else {
                    return redirect()->route('admin.practice-schedules.create')
                        ->with('success', 'Practice Schedules has been created');
                }
            } catch (Exception $e) {
                DB::rollBack();
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
            } catch (Throwable $e) {
                DB::rollBack();
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
            }
        } else {
            abort(403);
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
        $user = auth()->user();

        if ($user->can('update doctor schedules')) {
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
        } else {
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if ($user->can('update doctor schedules')) {
            $data = PracticeSchedule::find($id);
            $dateNow = Carbon::now()->format('Y-m-d');

            if ($request->date != null && $request->date < $dateNow) {
                return response()->json([
                    'status' => 400,
                    'errors' => 'Date must be higher than current date...!',
                ]);
            }

            $validator = Validator::make([
                'hospital'              => $request->hospital,
                'date'                  => $request->date,
                'doctor'                => $request->doctor,
                'start_time'            => $request->start_time,
                'end_time'              => $request->end_time,
            ], [
                'hospital'              => 'required',
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
                            'hospital_id'           => $request->hospital,
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
        } else {
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();

        if ($user->can('delete doctor schedules')) {
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
        } else {
            abort(403);
        }
    }
}
