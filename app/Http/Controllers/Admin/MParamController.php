<?php

namespace App\Http\Controllers\Admin;

use Throwable;
use App\Models\MParam;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class MParamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->type == 'datatable') {
            $data = MParam::with('hospital')->get();

            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    $user            = auth()->user();
                    $editRoute       = 'admin.setting-params.edit';
                    $deleteRoute     = 'admin.setting-params.destroy';
                    $dataId          = Crypt::encryptString($data->id);
                    $dataDeleteLabel = $data->hospital->name;

                    $action = "";

                    if ($user->can('update parameter')) {
                        $action .= '
                            <a class="btn btn-warning" id="btn-edit" type="button" data-url="' . route($editRoute, $dataId) . '">
                                <i class="fe fe-pencil"></i>
                            </a> ';
                    }

                    $group = '<div class="btn-group btn-group-sm mb-1 mb-md-0" role="group">
                        ' . $action . '
                    </div>';
                    return $group;
                })
                ->addColumn('hospital', function ($data) {
                    return $data->hospital->name;
                })
                ->rawColumns(['action', 'hospital'])
                ->make(true);
        }

        return view('admin.modules.settings.parameter.index', [
            'pageTitle'     => 'Setting Parameter',
            'breadcrumb'    => 'Setting Parameter',
            'hospitals'     => Hospital::get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort(403);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->can('create parameter')) {
            $validator = Validator::make([
                'format_surat'    => $request->format_surat,
                'hospital'        => $request->hospital
            ], [
                'format_surat'    => 'required|min:2|unique:m_params,format_surat,NULL,id',
                'hospital'        => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->messages(),
                ]);
            } else {
                DB::beginTransaction();
                try {
                    MParam::create([
                        'auto_no_surat'     => 1,
                        'format_surat'      => $request->format_surat,
                        'hospital_id'       => $request->hospital
                    ]);
                    DB::commit();
                    return response()->json([
                        'status'  => 200,
                        'message' => 'Parameter has been created',
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
    public function show(MParam $mParam)
    {
        abort(403);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = auth()->user();

        if ($user->can('update parameter')) {
            try {
                $id = Crypt::decryptString($id);
                $data = MParam::find($id);

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

        if ($user->can('update parameter')) {
            $data = MParam::find($id);

            $validator = Validator::make([
                'format_surat'    => $request->format_surat,
                'hospital'        => $request->hospital
            ], [
                'format_surat'    => 'required|min:2|unique:m_params,format_surat,' . $data->id,
                'hospital'        => 'required',
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
                            'format_surat'      => $request->format_surat,
                            'hospital_id'       => $request->hospital
                        ]);

                        DB::commit();

                        return response()->json([
                            'status'  => 200,
                            'message' => 'Parameter has been updated',
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
    public function destroy(MParam $mParam)
    {
        abort(403);
    }
}
