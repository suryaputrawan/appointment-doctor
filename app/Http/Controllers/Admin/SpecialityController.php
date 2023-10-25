<?php

namespace App\Http\Controllers\Admin;

use Throwable;
use App\Models\Speciality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SpecialityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->type == 'datatable') {
            $data = Speciality::query()->orderBy('name', 'asc')->get();

            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    $editRoute       = 'admin.speciality.edit';
                    $deleteRoute     = 'admin.speciality.destroy';
                    $viewRoute       = 'admin.speciality.show';
                    $dataId          = Crypt::encryptString($data->id);
                    $dataDeleteLabel = $data->name;

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
                ->addColumn('icon', function ($data) {
                    return $data->picture ? '<img src="' . $data->takePicture . '" alt="Gambar" width="50">' : '';
                })
                ->rawColumns(['action', 'icon'])
                ->make(true);
        }

        return view('admin.modules.speciality.index', [
            'pageTitle'     => 'Specialities',
            'breadcrumb'    => 'Specialities',
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
        $validator = Validator::make([
            'speciality'    => $request->speciality,
            'picture'       => $request->picture,
        ], [
            'speciality'    => 'required|max:100|min:2|unique:specialities,name,NULL,id',
            'picture'       => 'required|mimes:png|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            DB::beginTransaction();
            try {
                $karyawan = Speciality::create([
                    'name'          => $request->speciality,
                    'picture'       => request('picture') ? $request->file('picture')->store('images/specialities') : null
                ]);
                DB::commit();
                return response()->json([
                    'status'  => 200,
                    'message' => 'Speciality has been created',
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
    public function show(Speciality $speciality)
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
            $data = Speciality::find($id);

            if ($data) {
                return response()->json([
                    'status'    => 200,
                    'data'      => $data,
                ]);
            } else {
                return response()->json([
                    'status'    => 404,
                    'message'   => 'Speciality Not Found',
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
        // $id = Crypt::decryptString($id);
        $data = Speciality::find($id);

        $validator = Validator::make([
            'speciality'    => $request->speciality,
            'picture'       => $request->picture,
        ], [
            'speciality'    => 'required|max:100|min:2|unique:specialities,name,' . $data->id,
            'picture'       => request('picture') ? 'mimes:png|max:1000' : '',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            if ($data) {
                DB::beginTransaction();

                //Membuat kondisi langsung mendelete gambar yang lama pada storage
                if (request('picture')) {
                    if ($data->picture) {
                        Storage::delete($data->picture);
                    }
                    $picture = request()->file('picture')->store('images/specialities');
                } elseif ($data->picture) {
                    $picture = $data->picture;
                } else {
                    $picture = null;
                }

                try {
                    $data->update([
                        'name'          => $request->speciality,
                        'picture'       => $picture,
                    ]);

                    DB::commit();

                    return response()->json([
                        'status'  => 200,
                        'message' => 'Speciality has been updated',
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
            $data = Speciality::find($id);

            if (!$data) {
                return response()->json([
                    'status'  => 404,
                    'message' => "Data not found!",
                ], 404);
            }

            //Kondisi apabila terdapat path gambar pada tabel slider
            if ($data->picture != null) {
                Storage::delete($data->picture);
            }

            $data->delete();

            DB::commit();

            return response()->json([
                'status'  => 200,
                'message' => "Speciliaty has been deleted..!",
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 500,
                'message' => "Error on line {$e->getLine()}: {$e->getMessage()}",
            ], 500);
        }
    }
}
