<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorEducation;
use Illuminate\Support\Facades\Crypt;

class DoctorController extends Controller
{
    public function show(Request $request, $id)
    {
        $doctor = null;
        $doctorEducations = null;

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
        }

        return view('client.modules.doctor.detail', [
            'doctor'            => $doctor,
            'doctorEducations'  => $doctorEducations
        ]);
    }
}
