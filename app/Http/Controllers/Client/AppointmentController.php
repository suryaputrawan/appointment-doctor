<?php

namespace App\Http\Controllers\Client;

use Carbon\Carbon;
use App\Models\Doctor;
use Illuminate\Http\Request;
use App\Models\DoctorLocation;
use App\Models\PracticeSchedule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Support\Facades\Crypt;

class AppointmentController extends Controller
{
    public function booking($id)
    {
        try {
            $id = Crypt::decryptString($id);
            $data = Doctor::find($id);

            if (!$data) {
                return redirect()
                    ->back()
                    ->with('error', "Data not found..");
            }

            return view('client.modules.appointment.booking', [
                'breadcrumb'    => 'Booking',
                'btnSubmit'     => 'Make Appointment',
                'data'          => $data,
                'hospitals'     => DoctorLocation::with([
                    'hospital'  => function ($query) {
                        $query->select('id', 'name');
                    }
                ])
                    ->where('doctor_id', $data->id)->get(),
            ]);
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname'      => 'required|min:5',
            'dob'           => 'required',
            'sex'           => 'required',
            'email'         => 'required|email',
            'phone'         => 'required|min:7',
            'address'       => 'required|min:5',
            'hospital'      => 'required',
        ]);

        DB::beginTransaction();

        try {
            $dateNow = Carbon::now()->format('Y-m-d');

            $time = PracticeSchedule::where('id', $request->booking_time)->first();
            $bookingNumber = Appointment::whereDate('created_at', $dateNow)->get();

            $appointment = Appointment::create([
                'booking_number'    => Carbon::now()->format('Ymd') . $bookingNumber->count() + 1,
                'date'              => $time->date,
                'start_time'        => $time->start_time,
                'end_time'          => $time->end_time,
                'hospital_id'       => $request->hospital,
                'doctor_id'         => $request->doctor,
                'patient_name'      => $request->fullname,
                'patient_dob'       => $request->dob,
                'patient_sex'       => $request->sex,
                'patient_address'   => $request->address,
                'patient_email'     => $request->email,
                'patient_telp'      => $request->phone,
                'status'            => 'Booking',
            ]);

            $time->update([
                'booking_status'    => 1,
            ]);

            DB::commit();
            return redirect()->route('client.appointment.success', Crypt::encryptString($appointment->id));
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

    public function getBookingHospital(Request $request)
    {
        try {
            $id_doctor = $request->id_doctor;

            $data = DoctorLocation::with([
                'hospital'  => function ($query) {
                    $query->select('id', 'name');
                }
            ])
                ->where('doctor_id', $id_doctor)
                ->get();

            $option = "<option>-- Select Hospital --</option>";

            foreach ($data as $item) {
                $selectedState = '';
                if ($request->selected) {
                    $selectedState = $item->hospital_id == $request->selected ? 'selected' : '';
                }

                $hospital = json_decode($item->hospital);

                $option .=  "<option value='$item->hospital_id'$selectedState>$hospital->name</option>";
            }

            if ($data) {
                return response()->json([
                    'status' => 200,
                    'data' => $option,
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Hospital Not Found',
                ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 500,
                'message' => "Error on line {$e->getLine()}: {$e->getMessage()}",
            ], 500);
        }
    }

    public function getBookingDate(Request $request)
    {
        $dateNow = Carbon::now()->format('Y-m-d');

        try {
            $id_hospital = $request->id_hospital;
            $id_doctor = $request->id_doctor;

            $data = PracticeSchedule::where('hospital_id', $id_hospital)
                ->where('doctor_id', $id_doctor)
                ->where('date', '>=', $dateNow)
                ->get()->groupBy('date');

            $option = "<option>-- Select Date --</option>";

            foreach ($data as $index => $item) {
                $selectedState = '';
                if ($request->selected) {
                    $selectedState = $index == $request->selected ? 'selected' : '';
                }

                $date = Carbon::parse($index)->format('d M Y');

                $option .=  "<option value='$index'$selectedState>$date</option>";
            }

            if ($data) {
                return response()->json([
                    'status' => 200,
                    'data' => $option,
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Date Not Found',
                ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 500,
                'message' => "Error on line {$e->getLine()}: {$e->getMessage()}",
            ], 500);
        }
    }

    public function getBookingTime(Request $request)
    {
        try {
            $id_hospital    = $request->id_hospital;
            $id_doctor      = $request->id_doctor;
            $id_date        = $request->id_date;

            $data = PracticeSchedule::where('hospital_id', $id_hospital)
                ->where('doctor_id', $id_doctor)
                ->where('date', $id_date)
                ->where('booking_status', 0)
                ->orderBy('start_time', 'asc')
                ->get();

            $option = "<option>-- Select Time --</option>";

            foreach ($data as $item) {
                $selectedState = '';
                if ($request->selected) {
                    $selectedState = $item->id == $request->selected ? 'selected' : '';
                }

                $time = Carbon::parse($item->start_time)->format('H:i') . ' - ' .
                    Carbon::parse($item->end_time)->format('H:i') . ' Wita';

                $option .=  "<option value='$item->id' $selectedState>$time</option>";
            }

            if ($data->count() >= 1) {
                return response()->json([
                    'status' => 200,
                    'data' => $option,
                ]);
            } elseif ($data->count() == 0) {
                return response()->json([
                    'status' => 201,
                    'message' => 'No Available time slot / Tidak ada waktu kosong untuk tanggal tersebut',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Time Not Found',
                ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 500,
                'message' => "Error on line {$e->getLine()}: {$e->getMessage()}",
            ], 500);
        }
    }

    public function bookingSuccess($id)
    {
        try {
            $id = Crypt::decryptString($id);
            $data = Appointment::with([
                'hospital'  => function ($query) {
                    $query->select('id', 'name');
                },
                'doctor'    => function ($query) {
                    $query->select('id', 'name');
                }
            ])->find($id);

            if (!$data) {
                return redirect()
                    ->back()
                    ->with('error', "Data not found..");
            }

            return view('client.modules.appointment.booking-success', [
                'breadcrumb'    => 'Booking',
                'data'          => $data,
            ]);
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
        }
    }
}