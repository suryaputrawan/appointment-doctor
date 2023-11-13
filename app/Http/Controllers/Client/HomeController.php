<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Service;
use App\Models\Speciality;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with([
            'speciality'    => function ($query) {
                $query->select('id', 'name');
            },
            'doctorLocation'    => function ($query) {
                $query->with('hospital');
            },
            'practiceSchedules' => function ($query) {
                $query->where('date', '>=', Carbon::now()->format('Y-m-d'))
                    ->select('id', 'doctor_id', 'hospital_id', 'date', 'start_time', 'end_time', 'booking_status');
            }
        ])
            ->where('isAktif', 1)
            ->get();

        $specialities = Speciality::get(['id', 'name', 'picture']);

        $services = Service::orderBy('name', 'asc')->get(['id', 'name', 'description', 'picture']);

        return view('client.home', [
            'doctors'       => $doctors,
            'specialities'  => $specialities,
            'services'      => $services,
            'hospital'      => Hospital::get(['id', 'name'])
        ]);
    }
}
