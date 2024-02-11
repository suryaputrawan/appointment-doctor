<?php

namespace App\Http\Controllers\Client;

use Carbon\Carbon;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Speciality;
use Illuminate\Http\Request;
use App\Models\DoctorLocation;
use App\Models\DoctorEducation;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;

class DoctorController extends Controller
{
    public function show(Request $request, $id)
    {
        $doctor = null;
        $doctorEducations = null;
        $doctorLocations = null;

        try {
            $id = Crypt::decryptString($id);
            $doctor = Doctor::with('speciality')->find($id);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        if ($doctor) {
            $doctorEducations = DoctorEducation::where('doctor_id', $doctor->id)->get();
            $doctorLocations = DoctorLocation::with('hospital', 'doctorLocationDay')
                ->where('doctor_id', $doctor->id)
                ->orderBy('hospital_id')
                ->get();
        }

        return view('client.modules.doctor.detail', [
            'doctor'            => $doctor,
            'doctorEducations'  => $doctorEducations,
            'doctorLocations'   => $doctorLocations
        ]);
    }

    public function searchDoctor(Request $request)
    {
        $date = $request->date;
        $day = Carbon::parse($date)->format('l');
        $gender = [];
        $specialist = [];
        $hospital = [];

        if ($request->gender) {
            $gender = $_GET['gender'];
        }

        if ($request->specialist) {
            $specialist = $_GET['specialist'];
        }

        if ($request->hospital) {
            $hospital = $_GET['hospital'];
        }

        $doctors = Doctor::with([
            'speciality'    => function ($query) {
                $query->select('id', 'name', 'picture');
            },
            'doctorLocation'    => function ($query) {
                $query->select('id', 'doctor_id', 'hospital_id')
                    ->with('hospital', 'doctorLocationDay');
            },
            // 'practiceSchedules' => function ($query) {
            //     $query->where('date', '>=', Carbon::now()->format('Y-m-d'))
            //         ->select('id', 'doctor_id', 'hospital_id', 'date', 'start_time', 'end_time', 'booking_status');
            // }
        ])
            // ->when($date, function ($query) use ($date) {
            //     $query->whereHas('practiceSchedules', function ($q) use ($date) {
            //         $q->where('date', $date);
            //     });
            // })
            ->when($date, function ($query) use ($day) {
                $query->whereHas('doctorLocation', function ($q) use ($day) {
                    $q->whereHas('doctorLocationDay', function ($qu) use ($day) {
                        $qu->where('day', $day);
                    });
                });
            })
            ->when($gender, function ($query) use ($gender) {
                $query->whereIn('gender', $gender);
            })
            ->when($specialist, function ($query) use ($specialist) {
                $query->whereHas('speciality', function ($q) use ($specialist) {
                    $q->whereIn('name', $specialist);
                });
            })
            ->when($hospital, function ($query) use ($hospital) {
                $query->whereHas('doctorLocation', function ($q) use ($hospital) {
                    $q->whereHas('hospital', function ($qu) use ($hospital) {
                        $qu->whereIn('name', $hospital);
                    });
                });
            })
            ->where('isAktif', 1)
            ->get();

        return view('client.modules.doctor.search', [
            'doctors'       => $doctors,
            'speciality'    => Speciality::orderBy('name', 'asc')->get(),
            'hospital'      => Hospital::orderBy('name', 'asc')->get(['id', 'name'])
        ]);
    }
}
