<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Throwable;
use App\Models\Doctor;
use App\Models\Hospital;
use Illuminate\Http\Request;
use App\Models\DoctorLocation;
use App\Models\DoctorLocationDay;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\DoctorLocationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class DoctorLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if (request()->type == 'datatable') {
            if (!$user->hasRole('Super Admin|Admin')) {
                $data = DoctorLocation::with([
                    'doctorLocationDay' => function ($query) {
                        $query->select('id', 'doctor_location_id', 'day', 'start_time', 'end_time');
                    },
                    'doctor' => function ($query) {
                        $query->select('id', 'name');
                    },
                    'hospital'  => function ($query) {
                        $query->select('id', 'name');
                    }
                ])
                    ->where('hospital_id', $user->hospital_id)
                    ->whereHas('doctor', function ($query) {
                        $query->where('isAktif', 1);
                    })
                    ->orderBy('doctor_id')->get();
            } else {
                $data = DoctorLocation::with([
                    'doctorLocationDay' => function ($query) {
                        $query->select('id', 'doctor_location_id', 'day', 'start_time', 'end_time');
                    },
                    'doctor' => function ($query) {
                        $query->select('id', 'name');
                    },
                    'hospital'  => function ($query) {
                        $query->select('id', 'name');
                    }
                ])
                    ->whereHas('doctor', function ($query) {
                        $query->where('isAktif', 1);
                    })
                    ->orderBy('doctor_id')->get();
            }

            return datatables()->of($data)
                ->addColumn('action', function ($data) use ($user) {
                    $editRoute       = 'admin.doctor-location.edit';
                    $deleteRoute     = 'admin.doctor-location.destroy';
                    $viewRoute       = 'admin.doctor-location.show';
                    $dataId          = Crypt::encryptString($data->id);
                    $dataDeleteLabel = $data->doctor->name . ' - ' . $data->hospital->name;

                    $action = "";
                    $action .= '
                    <a class="btn btn-sm btn-primary" type="button" href="' . route($viewRoute, $dataId) . '">
                        <i class="fe fe-eye"></i>
                    </a> ';

                    if ($user->can('update doctors')) {
                        $action .= '
                            <a class="btn btn-sm btn-warning" type="button" href="' . route($editRoute, $dataId) . '">
                                <i class="fe fe-pencil"></i>
                            </a> ';
                    }

                    if ($user->can('delete doctors')) {
                        $action .= '
                        <button class="btn btn-sm btn-danger delete-item" 
                            data-label="' . $dataDeleteLabel . '" data-url="' . route($deleteRoute, $dataId) . '">
                            <i class="fe fe-trash"></i>
                        </button> ';
                    }

                    $group = '<div class="btn-group btn-group-sm mb-1 mb-md-0" role="group">
                        ' . $action . '
                    </div>';
                    return $group;
                })
                ->addColumn('doctor_name', function ($data) {
                    return $data->doctor->name;
                })
                ->addColumn('hospital', function ($data) {
                    return $data->hospital->name;
                })
                ->rawColumns(['action', 'doctor_name', 'hospital'])
                ->make(true);
        }

        return view('admin.modules.doctor-location.index', [
            'pageTitle'     => 'List of Doctor Practice Location',
            'breadcrumb'    => 'Doctor locations',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        $days = [
            'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
        ];

        $slotDuration = ['15', '30', '45', '60'];

        if (!$user->hasRole('Super Admin|Admin')) {
            $hospital = Hospital::orderBy('name', 'asc')->where('id', $user->hospital_id)
                ->get(['id', 'name']);
        } else {
            $hospital = Hospital::orderBy('name', 'asc')->get(['id', 'name']);
        }

        if ($user->can('create doctors')) {
            return view('admin.modules.doctor-location.create', [
                'pageTitle'     => 'Create Doctor Location',
                'breadcrumb'    => 'Create Doctor locations',
                'btnSubmit'     => 'Save',
                'hospital'      => $hospital,
                'doctor'        => Doctor::orderBy('name', 'asc')->get(['id', 'name']),
                'days'          => $days,
                'slotDuration'  => $slotDuration
            ]);
        } else {
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DoctorLocationRequest $request)
    {
        $user = auth()->user();

        if ($user->can('create doctors')) {
            try {
                DB::beginTransaction();

                $location = DoctorLocation::firstOrCreate([
                    'doctor_id'             => $request->doctor,
                    'hospital_id'           => $request->hospital,
                ]);

                //Store multiple data to doctor_location_day
                if ($request->day && $request->start_time && $request->end_time) {
                    for ($i = 0; $i < count($request->day); $i++) {
                        if ($request->day[$i]) {
                            DoctorLocationDay::firstOrCreate([
                                'doctor_location_id'    => $location->id,
                                'day'                   => $request->day[$i],
                                'start_time'            => $request->start_time[$i],
                                'end_time'              => $request->end_time[$i],
                                'duration'              => $request->duration[$i]
                            ]);
                        }
                    }
                }

                DB::commit();

                if (isset($_POST['btnSimpan'])) {
                    return redirect()->route('admin.doctor-location.index')
                        ->with('success', 'Doctor Location has been created');
                } else {
                    return redirect()->route('admin.doctor-location.create')
                        ->with('success', 'Doctor Location has been created');
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
    public function show($id)
    {
        try {
            $id = Crypt::decryptString($id);
            $data = DoctorLocation::find($id);
            $days = DoctorLocationDay::where('doctor_location_id', $data->id)->get();

            $dayArray = [
                'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
            ];

            $slotDuration = ['15', '30', '45', '60'];

            if (!$data) {
                return redirect()
                    ->back()
                    ->with('error', "Data not found..");
            }

            return view('admin.modules.doctor-location.view', [
                'pageTitle'     => 'View Doctor Location',
                'breadcrumb'    => 'View Doctor location',
                'data'          => $data,
                'days'          => $days,
                'dayArray'      => $dayArray,
                'slotDuration'  => $slotDuration,
                'hospitals'     => Hospital::orderBy('name', 'asc')->get(['id', 'name']),
                'doctors'       => Doctor::orderBy('name', 'asc')->get(['id', 'name'])
            ]);
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = auth()->user();

        if ($user->can('update doctors')) {
            try {
                $id = Crypt::decryptString($id);
                $data = DoctorLocation::find($id);
                $days = DoctorLocationDay::where('doctor_location_id', $data->id)->get();

                $dayArray = [
                    'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
                ];

                $slotDuration = ['15', '30', '45', '60'];

                if (!$data) {
                    return redirect()
                        ->back()
                        ->with('error', "Data not found..");
                }

                return view('admin.modules.doctor-location.edit', [
                    'pageTitle'     => 'Edit Doctor Location',
                    'breadcrumb'    => 'Edit Doctor location',
                    'btnSubmit'     => 'Save Change',
                    'data'          => $data,
                    'days'          => $days,
                    'dayArray'      => $dayArray,
                    'slotDuration'  => $slotDuration,
                    'hospitals'     => Hospital::orderBy('name', 'asc')->get(['id', 'name']),
                    'doctors'       => Doctor::orderBy('name', 'asc')->get(['id', 'name'])
                ]);
            } catch (\Throwable $e) {
                return redirect()
                    ->back()
                    ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
            }
        } else {
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DoctorLocationRequest $request, $id)
    {
        $user = auth()->user();

        if ($user->can('update doctors')) {
            $id = Crypt::decryptString($id);
            $data = DoctorLocation::find($id);
            $days = DoctorLocationDay::where('doctor_location_id', $data->id)->get();

            if (!$data) {
                return redirect()
                    ->back()
                    ->with('error', "Data not found");
            }

            DB::beginTransaction();

            try {
                $data->update([
                    'doctor_id'             => $request->doctor,
                    'hospital_id'           => $request->hospital,
                ]);

                //--Delete data collection
                foreach ($days as $day) {
                    $day->delete();
                }

                //Store multiple data to doctor_location_day
                if ($request->day && $request->start_time && $request->end_time) {
                    for ($i = 0; $i < count($request->day); $i++) {
                        if ($request->day[$i]) {
                            DoctorLocationDay::create([
                                'doctor_location_id'    => $data->id,
                                'day'                   => $request->day[$i],
                                'start_time'            => $request->start_time[$i],
                                'end_time'              => $request->end_time[$i],
                                'duration'              => $request->duration[$i],
                            ]);
                        }
                    }
                }

                DB::commit();

                return redirect()->route('admin.doctor-location.index')
                    ->with('success', 'Location success to updated');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
            } catch (\Throwable $e) {
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
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();

        if ($user->can('delete doctors')) {
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
        } else {
            abort(403);
        }
    }
}
