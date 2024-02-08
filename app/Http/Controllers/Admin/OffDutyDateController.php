<?php

namespace App\Http\Controllers\Admin;

use Throwable;
use Carbon\Carbon;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\OffDutyDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class OffDutyDateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if (request()->type == 'datatable') {
            if (!$user->hasRole('Super Admin|Admin')) {
                $data = OffDutyDate::with([
                    'doctor'    => function ($query) {
                        $query->select('id', 'name');
                    },
                ])->where('hospital_id', auth()->user()->hospital_id)
                    ->orderBy('date', 'desc')->get();
            } else {
                $data = OffDutyDate::with([
                    'doctor'    => function ($query) {
                        $query->select('id', 'name');
                    },
                ])->orderBy('date', 'desc')->get();
            }

            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    $user            = auth()->user();
                    $editRoute       = 'admin.off-duty.edit';
                    $deleteRoute     = 'admin.off-duty.destroy';
                    $viewRoute       = 'admin.off-duty.show';
                    $dataId          = Crypt::encryptString($data->id);
                    $dataDeleteLabel = $data->doctor->name . ' - ' . Carbon::parse($data->date)->format('d M Y');

                    $action = "";

                    if (Carbon::now()->format('Y-m-d') <= $data->date) {
                        if ($user->can('update off duty')) {
                            $action .= '
                            <a class="btn btn-warning" id="btn-edit" type="button" data-url="' . route($editRoute, $dataId) . '">
                                <i class="fe fe-pencil"></i>
                            </a> ';
                        }

                        if ($user->can('delete off duty')) {
                            $action .= '
                                <button class="btn btn-danger delete-item" 
                                    data-label="' . $dataDeleteLabel . '" data-url="' . route($deleteRoute, $dataId) . '">
                                    <i class="fe fe-trash"></i>
                                </button> ';
                        }
                    }

                    $group = '<div class="btn-group btn-group-sm mb-1 mb-md-0" role="group">
                        ' . $action . '
                    </div>';
                    return $group;
                })
                ->addColumn('doctor_name', function ($data) {
                    return $data->doctor->name;
                })
                ->addColumn('date', function ($data) {
                    return Carbon::parse($data->date)->format('d M Y');
                })
                ->addColumn('fasyankes', function ($data) {
                    return $data->hospital->name;
                })
                ->rawColumns(['action', 'doctor_name', 'date', 'hospital'])
                ->make(true);
        }

        if (!$user->hasRole('Super Admin|Admin')) {
            $doctor = Doctor::with([
                'doctorLocation'    => function ($query) {
                    $query->select('id', 'doctor_id', 'hospital_id');
                }
            ])
                ->whereHas('doctorLocation', function ($query) use ($user) {
                    $query->where('hospital_id', $user->hospital_id);
                })
                ->where('isAktif', 1)->orderBy('name', 'asc')
                ->get(['id', 'name']);

            $hospital = Hospital::orderBy('name', 'asc')->where('id', $user->hospital_id)
                ->get(['id', 'name']);
        } else {
            $doctor = Doctor::where('isAktif', 1)->orderBy('name', 'asc')->get(['id', 'name']);
            $hospital = Hospital::orderBy('name', 'asc')->get(['id', 'name']);
        }

        return view('admin.modules.off-duty.index', [
            'pageTitle'     => 'Off Duty Dates',
            'breadcrumb'    => 'Doctor Off Duty',
            'doctor'        => $doctor,
            'hospital'      => $hospital
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        if (!$user->can('create off duty')) {
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->can('create off duty')) {
            $validator = Validator::make([
                'doctor'        => $request->doctor,
                'fasyankes'      => $request->fasyankes,
                'date'          => $request->date,
                'reason'        => $request->reason
            ], [
                'doctor'        => 'required',
                'fasyankes'     => 'required',
                'date'          => 'required',
                'reason'        => 'required|min:3',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->messages(),
                ]);
            } else {
                DB::beginTransaction();
                try {
                    OffDutyDate::firstOrCreate([
                        'doctor_id'     => $request->doctor,
                        'date'          => $request->date,
                        'hospital_id'   => $request->fasyankes,
                        'reason'        => $request->reason
                    ]);
                    DB::commit();
                    return response()->json([
                        'status'  => 200,
                        'message' => 'Off duty doctor has been created',
                    ], 200);
                } catch (Throwable $th) {
                    DB::rollBack();
                    return response()->json([
                        'status'  => 500,
                        'message' => $th->getMessage(),
                    ], 500);
                }
            }
        } else {
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(OffDutyDate $offDutyDate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = auth()->user();

        if ($user->can('update off duty')) {
            try {
                $id = Crypt::decryptString($id);
                $data = OffDutyDate::find($id);

                if ($data) {
                    return response()->json([
                        'status'    => 200,
                        'data'      => $data,
                    ]);
                } else {
                    return response()->json([
                        'status'    => 404,
                        'message'   => 'Data Not Found',
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

        if ($user->can('update off duty')) {
            $data = OffDutyDate::find($id);

            $validator = Validator::make([
                'doctor'        => $request->doctor,
                'date'          => $request->date,
                'fasyankes'     => $request->fasyankes,
                'reason'        => $request->reason
            ], [
                'doctor'        => 'required',
                'date'          => 'required',
                'fasyankes'     => 'required',
                'reason'        => 'required|min:3',
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
                            'doctor_id'     => $request->doctor,
                            'date'          => $request->date,
                            'hospital_id'   => $request->fasyankes,
                            'reason'        => $request->reason
                        ]);

                        DB::commit();

                        return response()->json([
                            'status'  => 200,
                            'message' => 'Off duty doctor has been updated',
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

        if ($user->can('delete off duty')) {
            DB::beginTransaction();
            try {
                $id = Crypt::decryptString($id);
                $data = OffDutyDate::find($id);

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
                    'message' => "Off duty doctor has been deleted..!",
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
