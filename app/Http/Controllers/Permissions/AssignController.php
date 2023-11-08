<?php

namespace App\Http\Controllers\Permissions;

use Exception;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Spatie\Permission\Models\Permission;

class AssignController extends Controller
{
    public function index()
    {
        if (request()->type == 'datatable') {
            $data = Role::get();
            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    $editRoute       = 'admin.assign.edit';
                    $dataId          = Crypt::encryptString($data->id);

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
                ->addColumn('permissions', function ($data) {
                    return implode(', ', $data->getPermissionNames()->toArray());
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.modules.permission.assign.index', [
            'pageTitle'     => 'Assign Permissions',
            'breadcrumb'    => 'Assign Permissions'
        ]);
    }
    public function create()
    {
        return view('admin.modules.permission.assign.create', [
            'pageTitle'     => 'Create assign permissions',
            'breadcrumb'    => 'Create assign permissions',
            'btnSubmit'     => 'Save',
            'roles'         => Role::get(),
            'permissions'   => Permission::get()
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'role'          => 'required',
            'permissions'   => 'array|required'
        ]);

        try {
            DB::beginTransaction();

            $role = Role::find(request('role'));
            $role->givePermissionTo(request('permissions'));

            DB::commit();
            if (isset($_POST['btnSimpan'])) {
                return redirect()->route('admin.assign.index')
                    ->with('success', "Permission has been assigned to the role {$role->name}");
            } else {
                return redirect()->route('admin.assign.create')
                    ->with('success', "Permission has been assigned to the role {$role->name}");
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
    }
    public function edit($id)
    {
        try {
            $id = Crypt::decryptString($id);
            $data = Role::find($id);

            if (!$data) {
                return redirect()
                    ->back()
                    ->with('error', "Data not found..");
            }

            return view('admin.modules.permission.assign.edit', [
                'pageTitle'     => 'Sync assign permissions',
                'breadcrumb'    => 'Sync assign permissions',
                'btnSubmit'     => 'Sync',
                'data'          => $data,
                'roles'         => Role::get(),
                'permissions'   => Permission::get()
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
            'role'          => 'required',
            'permissions'   => 'array|required'
        ]);

        $id = Crypt::decryptString($id);
        $data = Role::find($id);

        if (!$data) {
            return redirect()
                ->back()
                ->with('error', "Data not found");
        }

        DB::beginTransaction();
        try {
            $request->validate([
                'role'          => 'required',
                'permissions'   => 'array|required'
            ]);
            $data->syncPermissions(request('permissions'));
            DB::commit();
            return redirect()->route('admin.assign.index')
                ->with('success', 'The Permission has been synced');
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
