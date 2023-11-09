<?php

namespace App\Http\Controllers\Permissions;

use Exception;
use Throwable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;

class UserController extends Controller
{
    public function index()
    {
        if (request()->type == 'datatable') {
            $data = User::has('roles')->where('isAktif', 1)->get();

            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    $editRoute      = 'admin.assign.user.edit';
                    $dataId         = Crypt::encryptString($data->id);

                    $action = "";

                    $action .= '
                    <a class="btn btn-warning btn-icon" type="button" href="' . route($editRoute, $dataId) . '">
                        <i class="fa fa-pencil"></i>
                    </a> ';

                    $group = '<div class="btn-group btn-group-sm mb-1 mb-md-0" role="group">
                        ' . $action . '
                    </div>';

                    return $group;
                })
                ->addColumn('role', function ($data) {
                    return implode(', ', $data->getRoleNames()->toArray());
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.modules.permission.assign.user.index', [
            'pageTitle'     => 'Permission To User',
            'breadcrumb'    => 'Permission To User'
        ]);
    }
    public function create()
    {
        return view('admin.modules.permission.assign.user.create', [
            'pageTitle'     => 'Create Permission To User',
            'breadcrumb'    => 'Create Permission To User',
            'btnSubmit'     => 'Save',
            'roles'         => Role::get(),
            'users'         => User::where('isAktif', 1)->get(['id', 'name', 'username'])
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'user'    => 'required',
            'roles'   => 'array|required'
        ]);

        try {
            DB::beginTransaction();

            $user = User::find($request->user);

            $user->assignRole(request('roles'));

            DB::commit();

            return redirect()->route('admin.assign.user.index')
                ->with('success', "{$user->name} has been assigned to the role");
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
    }
    public function edit($id)
    {
        try {
            $id = Crypt::decryptString($id);
            $data = User::find($id);

            if (!$data) {
                return redirect()
                    ->back()
                    ->with('error', "Data not found..");
            }

            return view('admin.modules.permission.assign.user.edit', [
                'pageTitle'     => 'Edit Permission To User',
                'breadcrumb'    => 'Edit Permission To User',
                'btnSubmit'     => 'Sync',
                'data'          => $data,
                'roles'         => Role::get(),
                'users'         => User::get(['id', 'name', 'username'])
            ]);
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
        }
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'user'      => 'required',
            'roles'     => 'array'
        ]);

        $id = Crypt::decryptString($id);
        $data = User::find($id);

        if (!$data) {
            return redirect()
                ->back()
                ->with('error', "Data not found");
        }

        DB::beginTransaction();
        try {
            $data->syncRoles(request('roles'));

            DB::commit();

            return redirect()->route('admin.assign.user.index')
                ->with('success', "{$data->name} has been updated the role");
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
    }
}
