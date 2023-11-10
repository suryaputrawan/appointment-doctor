<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\PracticeSchedule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->type == 'datatable') {
            $data = Appointment::with([
                'doctor'    =>  function ($query) {
                    $query->select('id', 'name');
                },
                'hospital'  => function ($query) {
                    $query->select('id', 'name');
                }
            ])
                ->orderBy('booking_number', 'desc')
                ->get();

            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    $user           = auth()->user();
                    $editRoute      = 'admin.appointment.edit';
                    $arrivedRoute   = 'admin.appointment.arrived';
                    $cancelRoute    = 'admin.appointment.cancel';
                    $viewRoute      = 'admin.appointment.show';
                    $dataId         = Crypt::encryptString($data->id);
                    $dataLabel      = $data->booking_number . ' - ' . $data->patient_name;
                    $dataName       = $data->patient_name;

                    $action = "";

                    if ($user->can('view appointment')) {
                        $action .= '
                            <a class="btn btn-sm btn-primary" id="btn-view" type="button" data-url="' . route($viewRoute, $dataId) . '"
                            data-name="' . $dataName . '">
                                <i class="fa fa-eye"></i>
                            </a> ';
                    }

                    if ($data->status == "Booking") {
                        if ($user->can('update appointment')) {
                            $action .= '
                                <a class="btn btn-sm btn-warning" type="button" href="' . route($editRoute, $dataId) . '">
                                    <i class="fa fa-pencil"></i>
                                </a> ';
                        }

                        if ($user->can('arrived appointment')) {
                            $action .= '
                                <button class="btn btn-sm btn-success arrived-item" 
                                    data-label="' . $dataLabel . '" data-url="' . route($arrivedRoute, $dataId) . '">
                                    <i class="fa fa-check"></i>
                                </button> ';
                        }

                        if ($user->can('cancel appointment')) {
                            $action .= '
                                <button class="btn btn-sm btn-danger cancel-item" 
                                    data-label="' . $dataLabel . '" data-url="' . route($cancelRoute, $dataId) . '">
                                    <i class="fa fa-ban"></i>
                                </button> ';
                        }
                    }

                    $group = '<div class="btn-group btn-group-sm mb-1 mb-md-0" role="group">
                        ' . $action . '
                    </div>';
                    return $group;
                })
                ->editColumn('date', function ($data) {
                    $bookingDate = Carbon::parse($data->date)->format('d M Y');
                    $bookingTime1 = Carbon::parse($data->start_time)->format('H:i');
                    $bookingTime2 = Carbon::parse($data->end_time)->format('H:i');

                    return $bookingDate . ' ' . $bookingTime1 . '-' . $bookingTime2;
                })
                ->addColumn('doctor', function ($data) {
                    return $data->doctor->name;
                })
                ->editColumn('status', function ($data) {
                    if ($data->status == "Booking") {
                        $status = '<button class="btn btn-sm btn-rounded btn-primary">Booking</button>';
                    } else if ($data->status == "Arrived") {
                        $status = '<button class="btn btn-sm btn-rounded btn-success">Arrived</button>';
                    } else {
                        $status = '<button class="btn btn-sm btn-rounded btn-danger">Cancel</button>';
                    }

                    return $status;
                })
                ->rawColumns(['action', 'doctor', 'date', 'status'])
                ->make(true);
        }

        return view('admin.modules.appointment.index', [
            'pageTitle'     => 'List of Appointment',
            'breadcrumb'    => 'Appointment',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        if ($user->can('create appointment')) {
            return view('admin.modules.appointment.create', [
                'pageTitle'     => 'Create Appointment',
                'breadcrumb'    => 'Create Appointment',
                'btnSubmit'     => 'Save',
                'doctor'        => Doctor::where('isAktif', 1)->orderBy('name', 'asc')->get(['id', 'name']),
            ]);
        } else {
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->can('create appointment')) {
            $request->validate([
                'patient_name'      => 'required|min:5',
                'dob'               => 'required',
                'gender'            => 'required',
                'email'             => 'required|email',
                'phone'             => 'required|min:7',
                'address'           => 'required|min:5',
                'doctor'            => 'required',
                'hospital'          => 'required',
                'booking_date'      => 'required',
                'booking_time'      => 'required'
            ]);

            DB::beginTransaction();

            try {
                $dateNow = Carbon::now()->format('Y-m-d');
                $time = PracticeSchedule::where('id', $request->booking_time)->first();
                $bookingNumber = Appointment::whereDate('created_at', $dateNow)->get();

                Appointment::create([
                    'booking_number'    => Carbon::now()->format('Ymd') . $bookingNumber->count() + 1,
                    'date'              => $time->date,
                    'start_time'        => $time->start_time,
                    'end_time'          => $time->end_time,
                    'hospital_id'       => $time->hospital_id,
                    'doctor_id'         => $request->doctor,
                    'patient_name'      => $request->patient_name,
                    'patient_dob'       => $request->dob,
                    'patient_sex'       => $request->gender,
                    'patient_address'   => $request->address,
                    'patient_email'     => $request->email,
                    'patient_telp'      => $request->phone,
                    'status'            => 'Booking',
                ]);

                $time->update([
                    'booking_status'    => 1,
                ]);

                DB::commit();
                if (isset($_POST['btnSimpan'])) {
                    return redirect()->route('admin.appointment.index')
                        ->with('success', 'Appointment has been created');
                } else {
                    return redirect()->route('admin.appointment.create')
                        ->with('success', 'Appointment has been created');
                }
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
        } else {
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $id = Crypt::decryptString($id);
            $data = Appointment::with([
                'doctor'    => function ($query) {
                    $query->select('id', 'name');
                },
                'hospital'  => function ($query) {
                    $query->select('id', 'name');
                }
            ])
                ->find($id);

            if ($data) {
                return response()->json([
                    'status'    => 200,
                    'data'      => $data,
                ]);
            } else {
                return response()->json([
                    'status'    => 404,
                    'message'   => 'Appointment Not Found',
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
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = auth()->user();

        if ($user->can('update appointment')) {
            try {
                $id = Crypt::decryptString($id);
                $data = Appointment::find($id);
                $schedule = PracticeSchedule::where('doctor_id', $data->doctor_id)
                    ->where('hospital_id', $data->hospital_id)
                    ->where('date', $data->date)
                    ->where('start_time', $data->start_time)
                    ->where('end_time', $data->end_time)
                    ->first();

                if (!$data) {
                    return redirect()
                        ->back()
                        ->with('error', "Data not found..");
                }

                return view('admin.modules.appointment.edit', [
                    'pageTitle'     => 'Edit Appointment',
                    'breadcrumb'    => 'Edit Appointment',
                    'btnSubmit'     => 'Save Change',
                    'data'          => $data,
                    'doctors'       => Doctor::where('isAktif', 1)->orderBy('name', 'asc')->get(['id', 'name']),
                    'schedule'      => $schedule
                ]);
            } catch (\Throwable $e) {
                return redirect()
                    ->back()
                    ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
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

        if ($user->can('update appointment')) {
            $request->validate([
                'patient_name'      => 'required|min:5',
                'dob'               => 'required',
                'gender'            => 'required',
                'email'             => 'required|email',
                'phone'             => 'required|min:7',
                'address'           => 'required|min:5',
                'doctor'            => 'required',
                'hospital'          => 'required',
                'booking_date'      => 'required',
                'booking_time'      => 'required'
            ]);

            $id = Crypt::decryptString($id);
            $data = Appointment::find($id);
            $time = PracticeSchedule::where('id', $request->booking_time)->first();

            if (!$data) {
                return redirect()
                    ->back()
                    ->with('error', "Data not found");
            }

            DB::beginTransaction();

            try {
                $data->update([
                    'date'                  => $request->booking_date,
                    'start_time'            => $time->start_time,
                    'end_time'              => $time->end_time,
                    'doctor_id'             => $request->doctor,
                    'hospital_id'           => $request->hospital,
                    'patient_name'          => $request->patient_name,
                    'patient_dob'           => $request->dob,
                    'patient_sex'           => $request->gender,
                    'patient_address'       => $request->address,
                    'patient_email'         => $request->email,
                    'patient_telp'          => $request->phone,
                ]);

                DB::commit();

                return redirect()->route('admin.appointment.index')
                    ->with('success', 'Appointment success to updated');
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
        } else {
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        //
    }

    public function arrived($id)
    {
        DB::beginTransaction();
        try {
            $decrypt = Crypt::decryptString($id);
            $data = Appointment::find($decrypt);

            if (!$data) {
                return response()->json([
                    'status'  => 404,
                    'message' => "Data not found!",
                ], 404);
            }

            $data->update([
                'status'     => 'Arrived',
                'user_id'    => Auth::user()->id
            ]);

            DB::commit();

            return response()->json([
                'status'  => 200,
                'message' => "Patient has been arrived",
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 500,
                'message' => "Error on line {$e->getLine()}: {$e->getMessage()}",
            ], 500);
        }
    }

    public function cancel($id)
    {
        DB::beginTransaction();
        try {
            $decrypt = Crypt::decryptString($id);
            $data = Appointment::find($decrypt);

            $schedule = PracticeSchedule::where('doctor_id', $data->doctor_id)
                ->where('hospital_id', $data->hospital_id)
                ->where('start_time', $data->start_time)
                ->where('end_time', $data->end_time)
                ->first();

            if (!$data) {
                return response()->json([
                    'status'  => 404,
                    'message' => "Data not found!",
                ], 404);
            }

            $data->update([
                'status'     => 'Cancel',
                'user_id'    => Auth::user()->id
            ]);

            $schedule->update([
                'booking_status'    => 0
            ]);

            DB::commit();

            return response()->json([
                'status'  => 200,
                'message' => "Patient has been cancel",
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 500,
                'message' => "Error on line {$e->getLine()}: {$e->getMessage()}",
            ], 500);
        }
    }
}
