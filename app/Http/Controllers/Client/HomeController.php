<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Service;
use App\Models\Speciality;
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
                $query->with('company');
            }
        ])->get();

        // dd($doctors);

        $specialities = Speciality::get(['id', 'name', 'picture']);

        $services = Service::orderBy('name', 'asc')->get(['id', 'name', 'description', 'picture']);

        return view('client.home', [
            'doctors'       => $doctors,
            'specialities'  => $specialities,
            'services'      => $services
        ]);
    }
}
