<?php

namespace App\Http\Controllers\Client;

use Carbon\Carbon;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Mail\BookingMail;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\DoctorLocation;
use App\Models\PracticeSchedule;
use Illuminate\Support\Facades\DB;
use App\Mail\BookingInfoDoctorMail;
use App\Http\Controllers\Controller;
use App\Models\DoctorLocationDay;
use App\Models\OffDutyDate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;

class AppointmentController extends Controller
{
    public function booking($slug)
    {
        try {
            // $id = Crypt::decryptString($id);
            // $data = Doctor::find($id);

            $data = Doctor::where('slug', $slug)->first();

            if (!$data) {
                return redirect()
                    ->back()
                    ->with('error', "Data not found..");
            }

            return view('client.modules.appointment.booking', [
                'breadcrumb'    => 'Booking',
                'btnSubmit'     => 'Book an Appointment',
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
            'fullname'          => 'required|min:5',
            'dob'               => 'required',
            'sex'               => 'required',
            'email'             => 'required|email',
            'phone'             => 'required|min:6',
            'address'           => 'required|min:5',
            'hospital'          => 'required',
            'booking_date'      => 'required',
            'booking_time'      => 'required',
            'symptoms'          => 'required|min:3'
        ]);

        $dateNow = Carbon::now()->format('Y-m-d');
        // $time = PracticeSchedule::where('id', $request->booking_time)->first();
        $bookingNumber = Appointment::whereDate('created_at', $dateNow)->get();

        DB::beginTransaction();

        try {

            $appointment = Appointment::where('date', $request->booking_day_date)
                ->where('start_time', Carbon::parse($request->booking_start_time)->format('H:i:s'))
                ->where('end_time', Carbon::parse($request->booking_end_time)->format('H:i:s'))
                ->where('hospital_id', $request->hospital)
                ->where('doctor_id', $request->doctor)
                ->where('status', '!=', 'Cancel')
                ->first();

            if ($appointment) {
                DB::rollBack();
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', "Time has been booked, please select another time or date");
            }

            // if ($time->booking_status == 1) {
            //     DB::rollBack();
            //     return redirect()
            //         ->back()
            //         ->withInput()
            //         ->with('error', "Time has been booked, please select another time or date");
            // }

            // $time->update([
            //     'booking_status'    => 1,
            // ]);

            $appointment = Appointment::create([
                'booking_number'    => Carbon::now()->format('Ymd') . $bookingNumber->count() + 1,
                // 'date'              => $time->date,
                // 'start_time'        => $time->start_time,
                // 'end_time'          => $time->end_time,
                // 'hospital_id'       => $request->hospital,
                'date'              => $request->booking_day_date,
                'start_time'        => Carbon::parse($request->booking_start_time)->format('H:i:s'),
                'end_time'          => Carbon::parse($request->booking_end_time)->format('H:i:s'),
                'hospital_id'       => $request->hospital,
                'doctor_id'         => $request->doctor,
                'patient_name'      => $request->fullname,
                'patient_dob'       => $request->dob,
                'patient_sex'       => $request->sex,
                'patient_address'   => $request->address,
                'patient_email'     => $request->email,
                'patient_telp'      => $request->phone,
                'status'            => 'Booking',
                'time_type'         => 'schedule',
                'patient_symptoms'  => $request->symptoms
            ]);

            $doctorMail = Doctor::where('id', $appointment->doctor_id)->first();
            $hospitalMail = Hospital::where('id', $appointment->hospital_id)->first();

            //--Send email
            Mail::to($appointment->patient_email)->send(new BookingMail($appointment));
            Mail::to($hospitalMail->email)->send(new BookingMail($appointment));
            Mail::to($doctorMail->email)->send(new BookingInfoDoctorMail($appointment));

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

            $option = "<option selected disabled>Select Clinic</option>";

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

            $option = "<option selected disabled>Select Date</option>";

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

            $hospital = Hospital::where('id', $id_hospital)->first();

            $option = "<option selected disabled>Select Time</option>";

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
                    'message' => 'Fully booked, please whatsapp admin on ' . $hospital->whatsapp . ' to book the appointment',
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

    public function getJadwalDokter(Request $request)
    {
        $jadwalPraktek = [];
        $startDate = Carbon::now()->startOfMonth();;
        $endDate = $startDate->copy()->addMonths(2);

        $currentDate = Carbon::parse($startDate);

        $id_hospital    = $request->id_hospital;
        $id_doctor      = $request->id_doctor;

        $offDates = OffDutyDate::where('doctor_id', $id_doctor)
            ->where('hospital_id', $id_hospital)->pluck('date');

        $doctorLocation = DoctorLocation::where('doctor_id', $id_doctor)
            ->where('hospital_id', $id_hospital)
            ->first();

        while ($currentDate->lte($endDate)) {
            $currentDayOfWeek = $currentDate->format('l');

            if (!$offDates->contains($currentDate->toDateString())) {
                $jadwal = DoctorLocationDay::where('day', $currentDayOfWeek)
                    ->where('doctor_location_id', $doctorLocation->id)
                    ->first();

                if ($jadwal && ($currentDate->isToday() || $currentDate->gt(now()))) {
                    $jadwalPraktek[] = [
                        'date' => $currentDate->toDateString(),
                    ];
                }
            }

            $currentDate->addDay();
        }

        $option = "<option selected disabled>Select Date</option>";

        foreach ($jadwalPraktek as $index => $item) {
            $selectedState = '';
            $index = $index + 1;

            if ($request->selected) {
                $selectedState = $index == $request->selected ? 'selected' : '';
            }

            $date = Carbon::parse($item['date'])->format('d M Y');
            $valDate = Carbon::parse($item['date'])->format('Y-m-d');
            $day = Carbon::parse($item['date'])->format('l');

            $option .=  "<option value='$index' $selectedState data-day='$day' data-date='$valDate'>$date</option>";
        }

        if ($jadwalPraktek) {
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
    }

    public function getWaktuDokter(Request $request)
    {
        $id_hospital    = $request->id_hospital;
        $id_doctor      = $request->id_doctor;
        $dateBooking    = $request->id_date;
        $dateDay        = $request->id_day;

        // Mengambil lokasi dokter berdasarkan id dokter dan id rumah sakit
        $doktorLocation = DoctorLocation::where('doctor_id', $id_doctor)
            ->where('hospital_id', $id_hospital)
            ->first();

        // Mengambil jadwal dokter berdasarkan lokasi dokter dan hari
        $doctorSchedules = DoctorLocationDay::where('doctor_location_id', $doktorLocation->id)
            ->where('day', $dateDay)
            ->get();

        // Mengambil data rumah sakit berdasarkan id rumah sakit
        $hospital = Hospital::where('id', $id_hospital)->first();

        // Menginisialisasi waktu saat ini
        $currentTime = Carbon::now('Asia/Singapore');

        if ($doctorSchedules->isNotEmpty()) {
            $waktu = []; // inisialisasi array di luar loop

            foreach ($doctorSchedules as $doctorSchedule) {
                // Mengambil waktu awal dan akhir dari jadwal dokter
                $waktuAwal = Carbon::parse($doctorSchedule->start_time);
                $waktuAkhir = Carbon::parse($doctorSchedule->end_time);
                $duration = $doctorSchedule->duration;
                $hasilWaktuAwal = [];

                // Looping untuk menentukan waktu yang tersedia
                while ($waktuAwal < $waktuAkhir) {
                    $hasilWaktuAwal[] = $waktuAwal->copy();
                    $waktuAwal->addMinutes($duration);
                }

                // Memeriksa konflik waktu dengan janji yang sudah ada
                foreach ($hasilWaktuAwal as $start) {
                    $end = $start->copy();
                    $end->addMinutes($duration);

                    $conflictExists = Appointment::where('doctor_id', $id_doctor)
                        ->where('hospital_id', $id_hospital)
                        ->where('date', $dateBooking)
                        ->where('start_time', $start)
                        ->where('end_time', $end)
                        ->where('status', '!=', 'Cancel')
                        ->exists();

                    // Menambahkan waktu yang tersedia jika tidak ada konflik
                    if (!$conflictExists) {
                        if ($dateBooking == $currentTime->toDateString()) {
                            if ($start->format('H:i:s') > $currentTime->format('H:i:s')) {
                                $waktu[] = [
                                    'start_time' => $start,
                                    'end_time' => $end
                                ];
                            }
                        } else {
                            $waktu[] = [
                                'start_time' => $start,
                                'end_time' => $end
                            ];
                        }
                    }
                }
            }

            // Setelah selesai mengolah semua jadwal
            if (!empty($waktu)) {
                // Memilih waktu yang tersedia
                $option = "<option selected disabled>Select Time</option>";
                foreach ($waktu as $index => $item) {
                    $selectedState = '';
                    $index = $index + 1;

                    if ($request->selected) {
                        $selectedState = $index == $request->selected ? 'selected' : '';
                    }

                    $start_time = Carbon::parse($item['start_time'])->format('H:i');
                    $end_time = Carbon::parse($item['end_time'])->format('H:i');
                    $time = "$start_time - $end_time Wita";

                    $option .=  "<option value='$index' $selectedState data-start-time='$start_time' data-end-time='$end_time'>$time</option>";
                }

                // Mengirim respons JSON dengan waktu yang tersedia
                return response()->json([
                    'status' => 200,
                    'data' => $option,
                ]);
            } else {
                // Jika tidak ada waktu yang tersedia
                return response()->json([
                    'status' => 201,
                    'message' => 'Fully booked, please whatsapp admin on ' . $hospital->whatsapp . ' to book the appointment',
                ]);
            }
        } else {
            // Jika tidak ada jadwal yang tersedia untuk hari tersebut
            return response()->json([
                'status' => 404,
                'message' => 'Doctor Schedule Not Found for the given day, please whatsapp admin on ' . $hospital->whatsapp . ' to book the appointment',
            ]);
        }
    }
}
