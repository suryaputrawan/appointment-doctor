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
use App\Mail\BookingInfoDoctorMail;
use App\Mail\BookingMail;
use App\Mail\RescheduleAppointmentMail;
use App\Mail\RescheduleInfoDoctorMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if (!$user->hasRole('Super Admin|Admin')) {
            $data = Appointment::with([
                'doctor'    =>  function ($query) {
                    $query->select('id', 'name');
                },
                'hospital'  => function ($query) {
                    $query->select('id', 'name');
                }
            ])
                ->when(request('date') != null && request('date') != 'NaN', function ($query) {
                    return $query->where('date', '=', request('date'));
                })
                ->when(request('doctor_id') != null && request('doctor_id') != 'NaN', function ($query) {
                    return $query->where('doctor_id', '=', request('doctor_id'));
                })
                ->where('hospital_id', $user->hospital_id)
                ->orderBy('booking_number', 'desc')
                ->get();

            $doctor = Doctor::with([
                'doctorLocation'    => function ($query) {
                    $query->select('id', 'doctor_id', 'hospital_id');
                }
            ])
                ->whereHas('doctorLocation', function ($query) use ($user) {
                    $query->where('hospital_id', $user->hospital_id);
                })
                ->where('isAktif', 1)->orderBy('name', 'asc')
                ->get(['id', 'name']);
        } else {
            $data = Appointment::with([
                'doctor'    =>  function ($query) {
                    $query->select('id', 'name');
                },
                'hospital'  => function ($query) {
                    $query->select('id', 'name');
                }
            ])
                ->when(request('date') != null && request('date') != 'NaN', function ($query) {
                    return $query->where('date', '=', request('date'));
                })
                ->when(request('doctor_id') != null && request('doctor_id') != 'NaN', function ($query) {
                    return $query->where('doctor_id', '=', request('doctor_id'));
                })
                ->orderBy('booking_number', 'desc')
                ->get();

            $doctor = Doctor::where('isAktif', 1)->orderBy('name', 'asc')->get(['id', 'name']);
        }

        if (request()->type == 'datatable') {

            return datatables()->of($data)
                ->addColumn('action', function ($data) use ($user) {
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

                    return $bookingDate . ' [ ' . $bookingTime1 . ' - ' . $bookingTime2 . ' Wita]';
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
                ->addColumn('reschedule', function ($data) {
                    $rescheduleRoute    = 'admin.appointment.reschedule';
                    $dataId             = Crypt::encryptString($data->id);

                    if ($data->status == "Booking") {
                        return '<a class="btn btn-sm btn-info" type="button" href="' . route($rescheduleRoute, $dataId) . '">
                            <i class="fa fa-calendar"></i>
                        </a> ';
                    }
                })
                ->rawColumns(['action', 'doctor', 'date', 'status', 'reschedule'])
                ->make(true);
        }

        return view('admin.modules.appointment.index', [
            'pageTitle'     => 'List of Appointment',
            'breadcrumb'    => 'Appointment',
            'doctor'        =>  $doctor
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        if (!$user->hasRole('Super Admin|Admin')) {
            $doctor = Doctor::with([
                'doctorLocation'    => function ($query) {
                    $query->select('id', 'doctor_id', 'hospital_id');
                }
            ])
                ->whereHas('doctorLocation', function ($query) use ($user) {
                    $query->where('hospital_id', $user->hospital_id);
                })
                ->where('isAktif', 1)->orderBy('name', 'asc')
                ->get(['id', 'name']);

            $hospitals = Hospital::where('id', $user->hospital_id)->get(['id', 'name']);
        } else {
            $doctor = Doctor::where('isAktif', 1)->orderBy('name', 'asc')->get(['id', 'name']);
            $hospitals = Hospital::get(['id', 'name']);
        }

        if ($user->can('create appointment')) {
            return view('admin.modules.appointment.create', [
                'pageTitle'     => 'Create Appointment',
                'breadcrumb'    => 'Create Appointment',
                'btnSubmit'     => 'Save',
                'doctor'        => $doctor,
                'hospitals'     => $hospitals
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
            if ($request->time_type == "schedule") {
                $request->validate([
                    'patient_name'      => 'required|min:5',
                    'dob'               => 'required',
                    'gender'            => 'required',
                    'email'             => 'required|email',
                    'phone'             => 'required|min:7',
                    'address'           => 'required|min:5',
                    'symptoms'          => 'required|min:3',
                    'doctor'            => 'required',
                    'hospital'          => 'required',
                    'booking_date'      => 'required',
                    'booking_time'      => 'required',
                    'time_type'         => 'required',
                ]);
            } else {
                $request->validate([
                    'patient_name'      => 'required|min:5',
                    'dob'               => 'required',
                    'gender'            => 'required',
                    'email'             => 'required|email',
                    'phone'             => 'required|min:7',
                    'address'           => 'required|min:5',
                    'symptoms'          => 'required|min:3',
                    'time_type'         => 'required',
                    'doctor_name'       => 'required',
                    'clinic_name'       => 'required',
                    'date_appointment'  => 'required',
                    'time_appointment'  => 'required'
                ]);
            }

            DB::beginTransaction();

            try {
                $dateNow = Carbon::now()->format('Y-m-d');
                // $time = PracticeSchedule::where('id', $request->booking_time)->first();
                $bookingNumber = Appointment::whereDate('created_at', $dateNow)->get();

                if ($request->time_type == 'schedule') {
                    $cekAppointmentSchedule = Appointment::where('date', $request->booking_day_date)
                        ->where('start_time', Carbon::parse($request->booking_start_time)->format('H:i:s'))
                        ->where('end_time', Carbon::parse($request->booking_end_time)->format('H:i:s'))
                        ->where('hospital_id', $request->hospital)
                        ->where('doctor_id', $request->doctor)
                        ->where('status', '!=', 'Cancel')
                        ->first();

                    if ($cekAppointmentSchedule) {
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->withInput()
                            ->with('error', "Time has been booked, please select another time or date");
                    } else {
                        $appointment = Appointment::create([
                            'booking_number'    => Carbon::now()->format('Ymd') . $bookingNumber->count() + 1,
                            // 'date'              => $time->date,
                            // 'start_time'        => $time->start_time,
                            // 'end_time'          => $time->end_time,
                            // 'hospital_id'       => $time->hospital_id,
                            'date'              => $request->booking_day_date,
                            'start_time'        => Carbon::parse($request->booking_start_time)->format('H:i:s'),
                            'end_time'          => Carbon::parse($request->booking_end_time)->format('H:i:s'),
                            'hospital_id'       => $request->hospital,
                            'doctor_id'         => $request->doctor,
                            'patient_name'      => $request->patient_name,
                            'patient_dob'       => $request->dob,
                            'patient_sex'       => $request->gender,
                            'patient_address'   => $request->address,
                            'patient_symptoms'  => $request->symptoms,
                            'patient_email'     => $request->email,
                            'patient_telp'      => $request->phone,
                            'status'            => 'Booking',
                            'user_id'           => auth()->user()->id,
                            'time_type'         => $request->time_type
                        ]);
                    }
                } else {
                    $cekAppointment = Appointment::where('date', $request->date_appointment)
                        ->where('hospital_id', $request->clinic_name)
                        ->where('start_time', Carbon::parse($request->time_appointment)->format('H:i:s'))
                        ->where('doctor_id', $request->doctor_name)
                        ->where('status', '!=', 'Cancel')
                        ->first();

                    if ($cekAppointment) {
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->withInput()
                            ->with('error', "Time has been booked, please select another time or date");
                    } else {
                        $appointment = Appointment::create([
                            'booking_number'    => Carbon::now()->format('Ymd') . $bookingNumber->count() + 1,
                            'date'              => $request->date_appointment,
                            'start_time'        => Carbon::parse($request->time_appointment)->format('H:i:s'),
                            'end_time'          => Carbon::parse($request->time_appointment)->addMinutes(30)->format('H:i:s'),
                            'hospital_id'       => $request->clinic_name,
                            'doctor_id'         => $request->doctor_name,
                            'patient_name'      => $request->patient_name,
                            'patient_dob'       => $request->dob,
                            'patient_sex'       => $request->gender,
                            'patient_address'   => $request->address,
                            'patient_symptoms'  => $request->symptoms,
                            'patient_email'     => $request->email,
                            'patient_telp'      => $request->phone,
                            'status'            => 'Booking',
                            'user_id'           => auth()->user()->id,
                            'time_type'         => $request->time_type
                        ]);
                    }
                }

                // $time->update([
                //     'booking_status'    => 1,
                // ]);

                $doctorMail = Doctor::where('id', $appointment->doctor_id)->first();
                $hospitalMail = Hospital::where('id', $appointment->hospital_id)->first();

                //--Send email
                Mail::to($appointment->patient_email)->send(new BookingMail($appointment));
                Mail::to($hospitalMail->email)->send(new BookingMail($appointment));
                Mail::to($doctorMail->email)->send(new BookingInfoDoctorMail($appointment));

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

        if (!$user->hasRole('Super Admin|Admin')) {
            $doctor = Doctor::with([
                'doctorLocation'    => function ($query) {
                    $query->select('id', 'doctor_id', 'hospital_id');
                }
            ])
                ->whereHas('doctorLocation', function ($query) use ($user) {
                    $query->where('hospital_id', $user->hospital_id);
                })
                ->where('isAktif', 1)->orderBy('name', 'asc')
                ->get(['id', 'name']);
        } else {
            $doctor = Doctor::where('isAktif', 1)->orderBy('name', 'asc')->get(['id', 'name']);
        }

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
                    'doctors'       => $doctor,
                    'schedule'      => $schedule,
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
                'symptoms'          => 'required|min:3'
            ]);

            $id = Crypt::decryptString($id);
            $data = Appointment::find($id);

            if (!$data) {
                return redirect()
                    ->back()
                    ->with('error', "Data not found");
            }

            DB::beginTransaction();

            try {
                $data->update([
                    'patient_name'          => $request->patient_name,
                    'patient_dob'           => $request->dob,
                    'patient_sex'           => $request->gender,
                    'patient_address'       => $request->address,
                    'patient_email'         => $request->email,
                    'patient_telp'          => $request->phone,
                    'patient_symptoms'      => $request->symptoms
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

    public function reschedule($id)
    {
        $user = auth()->user();

        if (!$user->hasRole('Super Admin|Admin')) {
            $doctor = Doctor::with([
                'doctorLocation'    => function ($query) {
                    $query->select('id', 'doctor_id', 'hospital_id');
                }
            ])
                ->whereHas('doctorLocation', function ($query) use ($user) {
                    $query->where('hospital_id', $user->hospital_id);
                })
                ->where('isAktif', 1)->orderBy('name', 'asc')
                ->get(['id', 'name']);

            $hospitals = Hospital::where('id', $user->hospital_id)->get(['id', 'name']);
        } else {
            $doctor = Doctor::where('isAktif', 1)->orderBy('name', 'asc')->get(['id', 'name']);

            $hospitals = Hospital::get(['id', 'name']);
        }

        if ($user->can('reschedule appointment')) {
            try {
                $id = Crypt::decryptString($id);
                $data = Appointment::find($id);
                // $schedule = PracticeSchedule::where('doctor_id', $data->doctor_id)
                //     ->where('hospital_id', $data->hospital_id)
                //     ->where('date', $data->date)
                //     ->where('start_time', $data->start_time)
                //     ->where('end_time', $data->end_time)
                //     ->first();

                if (!$data) {
                    return redirect()
                        ->back()
                        ->with('error', "Data not found..");
                }

                return view('admin.modules.appointment.reschedule', [
                    'pageTitle'     => 'Reschedule Appointment',
                    'breadcrumb'    => 'Reschedule Appointment',
                    'btnSubmit'     => 'Save Change',
                    'data'          => $data,
                    'doctors'       => $doctor,
                    // 'schedule'      => $schedule,
                    'hospitals'     => $hospitals
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

    public function rescheduleUpdate(Request $request, $id)
    {
        $user = auth()->user();

        if ($user->can('reschedule appointment')) {
            if ($request->time_type == 'schedule') {
                $request->validate([
                    'doctor'            => 'required',
                    'hospital'          => 'required',
                    'booking_date'      => 'required',
                    'booking_time'      => 'required'
                ]);
            } else {
                $request->validate([
                    'doctor_name'           => 'required',
                    'clinic_name'           => 'required',
                    'date_appointment'      => 'required',
                    'time_appointment'      => 'required'
                ]);
            }

            $id = Crypt::decryptString($id);
            $data = Appointment::find($id);

            // $schedule = PracticeSchedule::where('hospital_id', $data->hospital_id)
            //     ->where('doctor_id', $data->doctor_id)
            //     ->where('date', $data->date)
            //     ->where('start_time', $data->start_time)
            //     ->where('end_time', $data->end_time)->first();

            // $time = PracticeSchedule::where('id', $request->booking_time)->first();

            if (!$data) {
                return redirect()
                    ->back()
                    ->with('error', "Data not found");
            }

            DB::beginTransaction();

            try {
                if ($request->time_type == "schedule") {
                    $cekAppointmentSchedule = Appointment::where('date', $request->booking_day_date)
                        ->where('start_time', Carbon::parse($request->booking_start_time)->format('H:i:s'))
                        ->where('end_time', Carbon::parse($request->booking_end_time)->format('H:i:s'))
                        ->where('hospital_id', $request->hospital)
                        ->where('doctor_id', $request->doctor)->first();

                    if ($cekAppointmentSchedule) {
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->withInput()
                            ->with('error', "Time has been booked, please select another time or date");
                    } else {
                        $data->update([
                            'date'                  => $request->booking_day_date,
                            // 'start_time'            => $time->start_time,
                            // 'end_time'              => $time->end_time,
                            'start_time'            => $request->booking_start_time,
                            'end_time'              => $request->booking_end_time,
                            'doctor_id'             => $request->doctor,
                            'hospital_id'           => $request->hospital,
                        ]);
                    }
                } else {
                    $cekAppointment = Appointment::where('date', $request->date_appointment)
                        ->where('hospital_id', $request->clinic_name)
                        ->where('start_time', Carbon::parse($request->time_appointment)->format('H:i:s'))
                        ->where('doctor_id', $request->doctor_name)
                        ->first();

                    if ($cekAppointment) {
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->withInput()
                            ->with('error', "Time has been booked, please select another time or date");
                    } else {
                        $data->update([
                            'date'                  => $request->date_appointment,
                            'start_time'            => Carbon::parse($request->time_appointment)->format('H:i:s'),
                            'end_time'              => Carbon::parse($request->time_appointment)->addMinutes(30)->format('H:i:s'),
                            'doctor_id'             => $request->doctor_name,
                            'hospital_id'           => $request->clinic_name,
                        ]);
                    }
                }

                // $schedule->update([
                //     'booking_status'        => 0
                // ]);

                // $time->update([
                //     'booking_status'        => 1
                // ]);

                $doctorMail = Doctor::where('id', $data->doctor_id)->first();

                Mail::to($data->patient_email)->send(new RescheduleAppointmentMail($data));
                Mail::to($doctorMail->email)->send(new RescheduleInfoDoctorMail($data));

                DB::commit();

                return redirect()->route('admin.appointment.index')
                    ->with('success', 'Appointment success to reschedule');
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
}
