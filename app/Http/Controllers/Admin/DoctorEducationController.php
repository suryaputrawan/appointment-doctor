<?php

namespace App\Http\Controllers\Admin;

use Throwable;
use App\Models\Doctor;
use Illuminate\Http\Request;
use App\Models\DoctorEducation;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class DoctorEducationController extends Controller
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
                    $addRoute        = 'admin.doctor-education.list';
                    $dataId          = Crypt::encryptString($data->id);
                    $dataLabel       = $data->name;

                    $action = "";

                    $action .= '
                        <a class="btn btn-primary" id="btn-add" type="button" data-url="' . route($addRoute, $dataId) . '"
                         data-name="' . $dataLabel . '" data-id="' . $data->id . '">
                            <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                        </a> ';

                    $group = '<div class="btn-group btn-group-sm mb-1 mb-md-0" role="group">
                        ' . $action . '
                    </div>';
                    return $group;
                })
                ->addColumn('educations', function ($data) {
                    $education = DoctorEducation::where('doctor_id', $data->id)->get();

                    if ($education != null) {
                        return $data->doctorEducation()->implode('university_name', ', ');
                    } else {
                        return '';
                    }
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.modules.doctor-education.index', [
            'pageTitle'     => 'List of Doctor Education',
            'breadcrumb'    => 'Doctor Educations',
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function list($id)
    {
        if (request()->type == 'datatable') {
            $id = Crypt::decryptString($id);
            $data = DoctorEducation::with('doctor')->where('doctor_id', $id)->get();

            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    $editRoute       = 'admin.doctor-education.edit';
                    $deleteRoute     = 'admin.doctor-education.destroy';
                    $dataId          = Crypt::encryptString($data->id);
                    $dataName        = $data->doctor->name;
                    $dataDeleteLabel = $data->university_name;

                    $action = "";

                    $action .= '
                        <a class="btn btn-warning" id="btn-edit-education" type="button" data-url="' . route($editRoute, $dataId) . '"
                        data-name="' . $dataName . '" data-doctor-id="' . $data->doctor_id . '">
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
                ->addColumn('year', function ($data) {
                    return $data->start_year . ' - ' . $data->end_year;
                })
                ->rawColumns(['action', 'year'])
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
            'university'            => $request->university,
            'specialization'        => $request->specialization,
            'start_year'            => $request->start_year,
            'end_year'              => $request->end_year,
        ], [
            'university'            => 'required|min:5',
            'specialization'        => 'required|min:3',
            'start_year'            => 'required|min:4',
            'end_year'              => 'required|min:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            DB::beginTransaction();
            try {
                DoctorEducation::create([
                    'doctor_id'             => $request->doctor_id,
                    'university_name'       => $request->university,
                    'specialization'        => $request->specialization,
                    'start_year'            => $request->start_year,
                    'end_year'              => $request->end_year,
                ]);
                DB::commit();
                return response()->json([
                    'status'  => 200,
                    'message' => 'Education has been created',
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
    public function show(DoctorEducation $doctorEducation)
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
            $data = DoctorEducation::find($id);

            if ($data) {
                return response()->json([
                    'status'    => 200,
                    'data'      => $data,
                ]);
            } else {
                return response()->json([
                    'status'    => 404,
                    'message'   => 'Education Not Found',
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
        $data = DoctorEducation::find($id);

        $validator = Validator::make([
            'university'            => $request->university,
            'specialization'        => $request->specialization,
            'start_year'            => $request->start_year,
            'end_year'              => $request->end_year,
        ], [
            'university'            => 'required|min:5',
            'specialization'        => 'required|min:3',
            'start_year'            => 'required|min:4',
            'end_year'              => 'required|min:4',
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
                        'university_name'       => $request->university,
                        'specialization'        => $request->specialization,
                        'start_year'            => $request->start_year,
                        'end_year'              => $request->end_year,
                    ]);

                    DB::commit();

                    return response()->json([
                        'status'  => 200,
                        'message' => 'Education has been updated',
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
            $data = DoctorEducation::find($id);

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
                'message' => "Education has been deleted..!",
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 500,
                'message' => "Error on line {$e->getLine()}: {$e->getMessage()}",
            ], 500);
        }
    }
}
