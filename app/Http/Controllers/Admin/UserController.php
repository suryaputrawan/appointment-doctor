<?php

namespace App\Http\Controllers\Admin;

use Throwable;
use App\Models\User;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->type == 'datatable') {
            $data = User::with([
                'hospital'  => function ($query) {
                    $query->select('id', 'name');
                }
            ])
                ->orderBy('name', 'asc')->get();

            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    $user            = auth()->user();
                    $editRoute       = 'admin.users.edit';
                    $dataId          = Crypt::encryptString($data->id);

                    $action = "";

                    if ($user->can('update users')) {
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
                    return $data->hospital_id ? $data->hospital->name : '';
                })
                ->addColumn('status', function ($data) {
                    if ($data->isAktif == 0) {
                        return '<a class="btn btn-sm btn-danger" style="color:white">Non Aktif</a>';
                    } else {
                        return '<a class="btn btn-sm btn-success" style="color:white">Aktif</a>';
                    }
                })
                ->rawColumns(['action', 'hospital', 'status'])
                ->make(true);
        }

        return view('admin.modules.user.index', [
            'pageTitle'     => 'List of User',
            'breadcrumb'    => 'Users',
            'hospitals'     => Hospital::get(['id', 'name'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        if (!$user->can('create users')) {
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->can('create users')) {
            $validator = Validator::make([
                'name'                  => $request->name,
                'username'              => $request->username,
                'email'                 => $request->email,
                'hospital'              => $request->hospital,
            ], [
                'name'                  => 'required|min:3|unique:users,name',
                'username'              => 'required|min:5|unique:users,username',
                'email'                 => 'required|email|unique:users,email',
                'hospital'              => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->messages(),
                ]);
            } else {
                DB::beginTransaction();
                try {
                    if ($user->can('create users')) {
                        User::create([
                            'name'                  => $request->name,
                            'username'              => strtolower($request->username),
                            'email'                 => $request->email,
                            'hospital_id'           => $request->hospital,
                            'password'              => Hash::make('@12345678'),
                            'isAktif'               => $request->status
                        ]);
                    }

                    DB::commit();
                    return response()->json([
                        'status'  => 200,
                        'message' => 'User has been created',
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
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = auth()->user();

        if ($user->can('update users')) {
            try {
                $id = Crypt::decryptString($id);
                $data = User::find($id);

                if ($data) {
                    return response()->json([
                        'status'    => 200,
                        'data'      => $data,
                    ]);
                } else {
                    return response()->json([
                        'status'    => 404,
                        'message'   => 'User Not Found',
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

        if ($user->can('update users')) {
            $data = User::find($id);

            $validator = Validator::make([
                'name'                  => $request->name,
                'username'              => $request->username,
                'email'                 => $request->email,
                'hospital'              => $request->hospital,
            ], [
                'name'                  => 'required|min:3|unique:users,name,' . $data->id,
                'username'              => 'required|min:5|unique:users,username,' . $data->id,
                'email'                 => 'required|email|unique:users,email,' . $data->id,
                // 'hospital'              => 'required',
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
                            'name'                  => $request->name,
                            'username'              => strtolower($request->username),
                            'email'                 => $request->email,
                            'hospital_id'           => $request->hospital,
                            'isAktif'               => $request->status
                        ]);

                        DB::commit();

                        return response()->json([
                            'status'  => 200,
                            'message' => 'User has been updated',
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
    public function destroy(User $user)
    {
        //
    }
}
