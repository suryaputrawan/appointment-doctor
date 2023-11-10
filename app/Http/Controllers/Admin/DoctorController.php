<?php

namespace App\Http\Controllers\Admin;

use Throwable;
use App\Models\Doctor;
use App\Models\Speciality;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DoctorLocation;
use App\Models\DoctorEducation;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
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
                    $user            = auth()->user();
                    $editRoute       = 'admin.doctor.edit';
                    $deleteRoute     = 'admin.doctor.destroy';
                    $viewRoute       = 'admin.doctor.show';
                    $dataId          = Crypt::encryptString($data->id);
                    $dataDeleteLabel = $data->name;

                    $action = "";

                    if ($user->can('update doctors')) {
                        $action .= '
                            <a class="btn btn-warning" id="btn-edit" type="button" data-url="' . route($editRoute, $dataId) . '">
                                <i class="fe fe-pencil"></i>
                            </a> ';
                    }

                    if ($user->can('delete doctors')) {
                        // $action .= '
                        //     <button class="btn btn-danger delete-item" 
                        //         data-label="' . $dataDeleteLabel . '" data-url="' . route($deleteRoute, $dataId) . '">
                        //         <i class="fe fe-trash"></i>
                        //     </button> ';
                    }

                    $group = '<div class="btn-group btn-group-sm mb-1 mb-md-0" role="group">
                        ' . $action . '
                    </div>';
                    return $group;
                })
                ->addColumn('speciality', function ($data) {
                    return $data->speciality ? $data->speciality->name : '';
                })
                ->addColumn('doctor', function ($data) {
                    return $data->picture ? '<img class="avatar-img rounded-circle mr-3" src="' . $data->takePicture . '" alt="Gambar" width="50">' . $data->name : '';
                })
                ->addColumn('educations', function ($data) {
                    $education = DoctorEducation::where('doctor_id', $data->id)->get();

                    if ($education != null) {
                        return $data->doctorEducation()->implode('university_name', ', ');
                    } else {
                        return '';
                    }
                })
                ->addColumn('status', function ($data) {
                    if ($data->isAktif == 0) {
                        return '<a class="btn btn-sm btn-danger" style="color:white">Non Aktif</a>';
                    } else {
                        return '<a class="btn btn-sm btn-success" style="color:white">Aktif</a>';
                    }
                })
                ->rawColumns(['action', 'doctor', 'speciality', 'educations', 'status'])
                ->make(true);
        }

        return view('admin.modules.doctor.index', [
            'pageTitle'     => 'List of Doctors',
            'breadcrumb'    => 'Doctor List',
            'specialities'  => Speciality::orderBy('name', 'asc')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        if (!$user->can('create doctors')) {
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->can('create doctors')) {
            $validator = Validator::make([
                'name'                  => $request->name,
                'gender'                => $request->gender,
                'specialization'        => $request->specialization,
                'specialities'          => $request->specialities,
                'about_me'              => $request->about_me,
                'picture'               => $request->picture
            ], [
                'name'                  => 'required|max:100|min:5|unique:doctors,name,NULL,id',
                'gender'                => 'required',
                'specialization'        => 'required|min:3',
                'specialities'          => 'required',
                'about_me'              => 'required|min:5',
                'picture'               => 'required|mimes:jpg,jpeg,png|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->messages(),
                ]);
            } else {
                DB::beginTransaction();
                try {
                    Doctor::create([
                        'slug'                  => Str::slug($request->name),
                        'name'                  => $request->name,
                        'gender'                => $request->gender,
                        'specialization'        => $request->specialization,
                        'speciality_id'         => $request->specialities,
                        'about_me'              => $request->about_me,
                        'picture'               => request('picture') ? $request->file('picture')->store('images/doctors') : null,
                        'isAktif'               => $request->status
                    ]);
                    DB::commit();
                    return response()->json([
                        'status'  => 200,
                        'message' => 'Doctor has been created',
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
    public function show(Doctor $doctor)
    {
        //
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
                $data = Doctor::find($id);

                if ($data) {
                    return response()->json([
                        'status'    => 200,
                        'data'      => $data,
                    ]);
                } else {
                    return response()->json([
                        'status'    => 404,
                        'message'   => 'Doctor Not Found',
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

        if ($user->can('update doctors')) {
            $data = Doctor::find($id);

            $validator = Validator::make([
                'name'                  => $request->name,
                'gender'                => $request->gender,
                'specialization'        => $request->specialization,
                'specialities'          => $request->specialities,
                'about_me'              => $request->about_me,
                'picture'               => $request->picture
            ], [
                'name'                  => 'required|max:100|min:5|unique:doctors,name,' . $data->id,
                'gender'                => 'required',
                'specialization'        => 'required|min:3',
                'specialities'          => 'required',
                'about_me'              => 'required|min:5',
                'picture'               => request('picture') ? 'mimes:jpg,jpeg,png|max:1000' : '',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->messages(),
                ]);
            } else {
                if ($data) {
                    DB::beginTransaction();

                    //--Membuat kondisi langsung mendelete gambar yang lama pada storage
                    if (request('picture')) {
                        if ($data->picture) {
                            Storage::delete($data->picture);
                        }
                        $picture = request()->file('picture')->store('images/doctors');
                    } elseif ($data->picture) {
                        $picture = $data->picture;
                    } else {
                        $picture = null;
                    }
                    //--End

                    try {
                        $data->update([
                            'slug'                  => Str::slug($request->name),
                            'name'                  => $request->name,
                            'gender'                => $request->gender,
                            'specialization'        => $request->specialization,
                            'speciality_id'         => $request->specialities,
                            'about_me'              => $request->about_me,
                            'picture'               => $picture,
                            'isAktif'               => $request->status
                        ]);

                        DB::commit();

                        return response()->json([
                            'status'  => 200,
                            'message' => 'Doctor has been updated',
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

        if ($user->can('delete doctors')) {
            DB::beginTransaction();
            try {
                $id = Crypt::decryptString($id);
                $data = Doctor::find($id);
                $doctorEducations = DoctorEducation::where('doctor_id', $id)->get();
                $doctorLocations = DoctorLocation::where('doctor_id', $id)->get();

                if (!$data) {
                    return response()->json([
                        'status'  => 404,
                        'message' => "Data not found!",
                    ], 404);
                }

                //Kondisi apabila terdapat path gambar pada tabel
                if ($data->picture != null) {
                    Storage::delete($data->picture);
                    $data->delete();
                } else {
                    $data->delete();
                }
                //End kondisi

                //---Check apakah terdapat data pada tabel education
                if ($doctorEducations != null) {
                    foreach ($doctorEducations as $doctorEducation) {
                        $doctorEducation->delete();
                    }
                }
                //---End check

                //---Check apakah terdapat data pada tabel doctor location
                if ($doctorLocations != null) {
                    foreach ($doctorLocations as $doctorLocation) {
                        $doctorLocation->delete();
                    }
                }
                //---End check

                DB::commit();

                return response()->json([
                    'status'  => 200,
                    'message' => "Doctor has been deleted..!",
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
