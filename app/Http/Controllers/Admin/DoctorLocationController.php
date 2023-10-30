<?php

namespace App\Http\Controllers\Admin;

use Throwable;
use App\Models\Doctor;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\DoctorLocation;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class DoctorLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->type == 'datatable') {
            $data = Doctor::query()->orderBy('name', 'asc')->get();

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
                ->addColumn('locations_count', function ($data) {
                    $location = DoctorLocation::with('company')->where('doctor_id', $data->id)
                        ->get();

                    if ($location != null) {
                        return $location->count();
                    } else {
                        return '';
                    }
                })
                ->rawColumns(['action', 'locations_count'])
                ->make(true);
        }

        return view('admin.modules.doctor-location.index', [
            'pageTitle'     => 'List of Doctor Practice Location',
            'breadcrumb'    => 'Doctor locations',
            'companies'     => Company::orderBy('name', 'asc')->get(['id', 'name'])
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
                'company'   => function ($query) {
                    $query->select('id', 'name');
                }
            ])
                ->where('doctor_id', $id)->get();

            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    $editRoute       = 'admin.doctor-location.edit';
                    $deleteRoute     = 'admin.doctor-location.destroy';
                    $dataId          = Crypt::encryptString($data->id);
                    $dataName        = $data->doctor->name;
                    $dataDeleteLabel = $data->company->name;

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
                ->addColumn('company', function ($data) {
                    return $data->company->name;
                })
                ->rawColumns(['action', 'company'])
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
            DB::beginTransaction();
            try {
                DoctorLocation::create([
                    'doctor_id'             => $request->doctor_id,
                    'company_id'            => $request->location,
                    'day'                   => $request->day,
                    'time'                  => $request->time,
                ]);
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

            if ($data) {
                return response()->json([
                    'status'    => 200,
                    'data'      => $data,
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
                        'company_id'            => $request->location,
                        'day'                   => $request->day,
                        'time'                  => $request->time,
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
