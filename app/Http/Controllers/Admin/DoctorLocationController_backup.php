<?php

namespace App\Http\Controllers\Admin;

use Throwable;
use App\Models\Doctor;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\DoctorLocation;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\DoctorLocationRequest;
use App\Models\DoctorLocationDay;
use App\Models\Hospital;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class DoctorLocationControllerBackup extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->type == 'datatable') {
            $data = Doctor::with([
                'doctorLocation'    => function ($query) {
                    $query->select('id', 'doctor_id', 'hospital_id')
                        ->with([
                            'hospital' => function ($q) {
                                $q->select('id', 'name');
                            }
                        ]);
                }
            ])
                ->orderBy('name', 'asc')->get();

            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    $addRoute        = 'admin.doctor-location.list';
                    $dataId          = Crypt::encryptString($data->id);
                    $dataLabel       = $data->name;

                    $action = "";

                    $action .= '
                        <a class="btn btn-primary" id="btn-add" type="button" data-url="' . route($addRoute, $dataId) . '"
                         data-name="' . $dataLabel . '" data-id="' . $data->id . '">
                            <i class="fa fa-hospital-o" aria-hidden="true"></i>
                        </a> ';

                    $group = '<div class="btn-group btn-group-sm mb-1 mb-md-0" role="group">
                        ' . $action . '
                    </div>';
                    return $group;
                })
                ->addColumn('hospital', function ($data) {
                    $location = DoctorLocation::with('hospital')
                        ->where('doctor_id', $data->id)
                        ->get();

                    foreach ($location as $value) {
                        return $value->hospital->implode('name', ', ');
                    }
                })
                ->rawColumns(['action', 'locations_count', 'hospital'])
                ->make(true);
        }

        return view('admin.modules.doctor-location.index', [
            'pageTitle'     => 'List of Doctor Practice Location',
            'breadcrumb'    => 'Doctor locations',
            'hospital'      => Hospital::orderBy('name', 'asc')->get(['id', 'name'])
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function list($id)
    {
        if (request()->type == 'datatable') {
            $id = Crypt::decryptString($id);
            $data = DoctorLocation::with([
                'doctor'    => function ($query) {
                    $query->select('id', 'name');
                },
                'hospital'   => function ($query) {
                    $query->select('id', 'name');
                },
                'doctorLocationDay' => function ($query) {
                    $query->select('id', 'doctor_location_id', 'day', 'time');
                }
            ])
                ->where('doctor_id', $id)->get();

            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    $editRoute       = 'admin.doctor-location.edit';
                    $deleteRoute     = 'admin.doctor-location.destroy';
                    $dataId          = Crypt::encryptString($data->id);
                    $dataName        = $data->doctor->name;
                    $dataDeleteLabel = $data->hospital->name;

                    $action = "";

                    $action .= '
                        <a class="btn btn-sm btn-warning" id="btn-edit-location" type="button" data-url="' . route($editRoute, $dataId) . '"
                        data-name="' . $dataName . '" data-doctor-id="' . $data->doctor_id . '">
                            <i class="fe fe-pencil"></i>
                        </a> ';

                    $action .= '
                        <button class="btn btn-sm btn-danger delete-item" 
                            data-label="' . $dataDeleteLabel . '" data-url="' . route($deleteRoute, $dataId) . '">
                            <i class="fe fe-trash"></i>
                        </button> ';

                    $group = '<div class="btn-group btn-group-sm mb-1 mb-md-0" role="group">
                        ' . $action . '
                    </div>';
                    return $group;
                })
                ->addColumn('hospital', function ($data) {
                    return $data->hospital->name;
                })
                ->addColumn('day', function ($data) {
                    return implode(', ', $data->doctorLocationDay->pluck('day')->toArray());
                })
                ->addColumn('time', function ($data) {
                    return implode(', ', $data->doctorLocationDay->pluck('time')->toArray());
                })
                ->rawColumns(['action', 'hospital', 'day', 'time'])
                ->make(true);
        }
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
        $validator = Validator::make([
            'location'      => $request->location,
            'day'           => $request->day,
            'time'          => $request->time,
        ], [
            'location'      => 'required|unique:doctor_locations,hospital_id,NULL,id',
            'day'           => 'array',
            'day.*'         => 'required|min:3',
            'time'          => 'array',
            'time.*'        => 'required|min:5',
        ]);

        // $validator = $request;

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            DB::beginTransaction();
            try {
                $location = DoctorLocation::create([
                    'doctor_id'             => $request->doctor_id,
                    'hospital_id'           => $request->location,
                ]);

                //Store multiple data to doctor_location_day
                if ($request->day && $request->time) {
                    for ($i = 0; $i < count($request->day); $i++) {
                        if ($request->day[$i]) {
                            DoctorLocationDay::create([
                                'doctor_location_id'    => $location->id,
                                'day'                   => strtoupper($request->day[$i]),
                                'time'                  => strtoupper($request->time[$i]),
                            ]);
                        }
                    }
                }
                // DoctorLocationDay::create([
                //     'doctor_location_id'    => $location->id,
                //     'day'                   => strtoupper($request->day),
                //     'time'                  => strtoupper($request->time),
                // ]);

                DB::commit();
                return response()->json([
                    'status'  => 200,
                    'message' => 'Doctor Location has been created',
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
    public function show(DoctorLocation $doctorLocation)
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
            $data = DoctorLocation::find($id);
            $locationDay = DoctorLocationDay::where('doctor_location_id', $data->id)->get();

            if ($data) {
                return response()->json([
                    'status'    => 200,
                    'data'      => $data,
                    'day'       => $locationDay,
                ]);
            } else {
                return response()->json([
                    'status'    => 404,
                    'message'   => 'Location Not Found',
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
        $data = DoctorLocation::find($id);
        $locationDay = DoctorLocationDay::where('doctor_location_id', $data->id)->first();

        $validator = Validator::make([
            'location'      => $request->location,
            'day'           => $request->day,
            'time'          => $request->time,
        ], [
            'location'      => 'required',
            'day'           => 'required|min:3',
            'time'          => 'required|min:5',
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
                        'hospital_id'           => $request->location,
                    ]);

                    $locationDay->update([
                        'day'                   => strtoupper($request->day),
                        'time'                  => strtoupper($request->time),
                    ]);

                    DB::commit();

                    return response()->json([
                        'status'  => 200,
                        'message' => 'Doctor location has been updated',
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
            $data = DoctorLocation::find($id);
            $locationDay = DoctorLocationDay::where('doctor_location_id', $data->id)->get();

            if (!$data) {
                return response()->json([
                    'status'  => 404,
                    'message' => "Data not found!",
                ], 404);
            }

            $data->delete();

            //--Delete data collection
            foreach ($locationDay as $day) {
                $day->delete();
            }

            DB::commit();

            return response()->json([
                'status'  => 200,
                'message' => "Location has been deleted..!",
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 500,
                'message' => "Error on line {$e->getLine()}: {$e->getMessage()}",
            ], 500);
        }
    }
}
