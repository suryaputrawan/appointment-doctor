<?php

namespace App\Http\Controllers\Admin;

use Throwable;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class HospitalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->type == 'datatable') {
            $data = Hospital::query()->orderBy('name', 'asc')->get();

            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    $editRoute       = 'admin.hospitals.edit';
                    $deleteRoute     = 'admin.hospitals.destroy';
                    $viewRoute       = 'admin.hospitals.show';
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
                ->addColumn('hospital_name', function ($data) {
                    return $data->logo ? '<img class="mr-3" src="' . $data->takeLogo . '" alt="Gambar" width="50">' . $data->name : '';
                })
                ->rawColumns(['action', 'hospital_name'])
                ->make(true);
        }

        return view('admin.modules.hospital.index', [
            'pageTitle'     => 'List Of Hospital',
            'breadcrumb'    => 'Hospitals',
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
            'name'                  => $request->name,
            'address'               => $request->address,
            'phone'                 => $request->phone,
            'whatsapp'              => $request->whatsapp,
            'email'                 => $request->email,
            'instagram'             => $request->instagram,
            'facebook'              => $request->facebook,
            'logo'                  => $request->logo
        ], [
            'name'                  => 'required|max:100|min:5|unique:hospitals,name,NULL,id',
            'address'               => 'required|min:5',
            'phone'                 => 'required|min:7',
            'whatsapp'              => 'required|min:9',
            'email'                 => 'required|email|unique:hospitals,email',
            'logo'                  => 'required|mimes:png|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            DB::beginTransaction();
            try {
                Hospital::create([
                    'name'                  => $request->name,
                    'address'               => $request->address,
                    'phone'                 => $request->phone,
                    'whatsapp'              => $request->whatsapp,
                    'email'                 => $request->email,
                    'instagram'             => $request->instagram,
                    'facebook'              => $request->facebook,
                    'logo'                  => request('logo') ? $request->file('logo')->store('images/hospital/logo') : null,
                ]);
                DB::commit();
                return response()->json([
                    'status'  => 200,
                    'message' => 'Hospital has been created',
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
    public function show(Hospital $hospital)
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
            $data = Hospital::find($id);

            if ($data) {
                return response()->json([
                    'status'    => 200,
                    'data'      => $data,
                ]);
            } else {
                return response()->json([
                    'status'    => 404,
                    'message'   => 'Hospital Not Found',
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
        $data = Hospital::find($id);

        $validator = Validator::make([
            'name'                  => $request->name,
            'address'               => $request->address,
            'phone'                 => $request->phone,
            'whatsapp'              => $request->whatsapp,
            'email'                 => $request->email,
            'instagram'             => $request->instagram,
            'facebook'              => $request->facebook,
            'logo'                  => $request->logo,
        ], [
            'name'                  => 'required|max:100|min:5|unique:hospitals,name,' . $data->id,
            'address'               => 'required|min:5',
            'phone'                 => 'required|min:7',
            'whatsapp'              => 'required|min:9',
            'email'                 => 'required|email|unique:hospitals,email,' . $data->id,
            'logo'                  => request('logo') ? 'mimes:png|max:1000' : '',
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
                if (request('logo')) {
                    if ($data->logo) {
                        Storage::delete($data->logo);
                    }
                    $logo = request()->file('logo')->store('images/hospital/logo');
                } elseif ($data->logo) {
                    $logo = $data->logo;
                } else {
                    $logo = null;
                }
                //--End

                try {
                    $data->update([
                        'name'                  => $request->name,
                        'address'               => $request->address,
                        'phone'                 => $request->phone,
                        'whatsapp'              => $request->whatsapp,
                        'email'                 => $request->email,
                        'instagram'             => $request->instagram,
                        'facebook'              => $request->facebook,
                        'logo'                  => $logo
                    ]);

                    DB::commit();

                    return response()->json([
                        'status'  => 200,
                        'message' => 'Hospital has been updated',
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
            $data = Hospital::find($id);

            if (!$data) {
                return response()->json([
                    'status'  => 404,
                    'message' => "Data not found!",
                ], 404);
            }

            //Kondisi apabila terdapat path gambar pada tabel
            if ($data->logo != null) {
                Storage::delete($data->logo);
                $data->delete();
            } else {
                $data->delete();
            }
            //End kondisi

            DB::commit();

            return response()->json([
                'status'  => 200,
                'message' => "Hospital has been deleted..!",
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 500,
                'message' => "Error on line {$e->getLine()}: {$e->getMessage()}",
            ], 500);
        }
    }
}
