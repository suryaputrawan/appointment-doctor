<?php

namespace App\Http\Controllers\Admin;

use Throwable;
use Carbon\Carbon;
use App\Models\MParam;
use App\Models\SickLetter;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
// use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Mail\SickLetterMail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PDF;

class SickLetterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if (request()->type == 'datatable') {
            if (!$user->hasRole('Super Admin|Admin')) {
                $data = SickLetter::with('user')
                    ->where('created_by', $user->id)
                    ->get();
            } else {
                $data = SickLetter::with('user')->get();
            }

            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    $user            = auth()->user();
                    $editRoute       = 'admin.sick-letters.edit';
                    $deleteRoute     = 'admin.sick-letters.destroy';
                    $printRoute      = 'admin.sick-letter.print';
                    $dataId          = Crypt::encryptString($data->id);
                    $dataDeleteLabel = $data->nomor;

                    $createdDate = Carbon::parse($data->created_at)->format('Y-m-d');
                    $dateNow = Carbon::now('Asia/Singapore')->format('Y-m-d');

                    $action = "";

                    if ($user->can('update sick letter')) {
                        if ($createdDate >= $dateNow) {
                            $action .= '
                                <a class="btn btn-warning" id="btn-edit" type="button" data-url="' . route($editRoute, $dataId) . '">
                                    <i class="fe fe-pencil"></i>
                                </a> ';
                        }
                    }

                    if ($user->can('delete sick letter')) {
                        if ($createdDate >= $dateNow) {
                            $action .= '
                            <button class="btn btn-danger delete-item" 
                                data-label="' . $dataDeleteLabel . '" data-url="' . route($deleteRoute, $dataId) . '">
                                <i class="fe fe-trash"></i>
                            </button> ';
                        }
                    }

                    if ($user->can('print sick letter')) {
                        $action .= '
                        <a class="btn btn-success" type="button" target="_blank" href="' . route($printRoute, $dataId) . '">
                            <i class="fa fa-print"></i>
                        </a> ';
                    }

                    $group = '<div class="btn-group btn-group-sm mb-1 mb-md-0" role="group">
                        ' . $action . '
                    </div>';
                    return $group;
                })
                ->addColumn('user', function ($data) {
                    return $data->user->name;
                })
                ->rawColumns(['action', 'user'])
                ->make(true);
        }

        return view('admin.modules.sick-letter.index', [
            'pageTitle'     => 'Sick Letters',
            'breadcrumb'    => 'Sick Letters',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort(403);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->can('create sick letter')) {
            $validator = Validator::make([
                'name'              => $request->name,
                'patient_email'     => $request->patient_email,
                'age'               => $request->age,
                'gender'            => $request->gender,
                'profession'        => $request->profession,
                'address'           => $request->address,
                'start_date'        => $request->start_date,
                'end_date'          => $request->end_date,
                'diagnosis'         => $request->diagnosis
            ], [
                'name'              => 'required|min:5',
                'patient_email'     => 'required',
                'age'               => 'required',
                'gender'            => 'required',
                'address'           => 'required',
                'start_date'        => 'required',
                'end_date'          => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->messages(),
                ]);
            } else {
                DB::beginTransaction();

                $year = Carbon::now()->format('Y');
                $month = Carbon::now()->format('m');
                $romawi = getRomawi($month);

                $nomor = MParam::where('hospital_id', auth()->user()->hospital_id)->first();
                $autoNomor = $nomor->auto_no_surat;
                $nomorSurat = str_pad($autoNomor, 3, '0', STR_PAD_LEFT) . '/' . $nomor->format_surat . '/' . $romawi . '/' . $year;

                $slug = str_replace('/', '-', $nomorSurat);

                // Gunakan Str::slug untuk memastikan format yang sesuai untuk URL
                $slug = Str::slug($slug, '-');

                try {
                    $sickLetter = SickLetter::create([
                        'slug'              => $slug,
                        'nomor'             => $nomorSurat,
                        'date'              => Carbon::now('Asia/Singapore'),
                        'patient_name'      => $request->name,
                        'patient_email'     => $request->patient_email,
                        'age'               => $request->age,
                        'gender'            => $request->gender,
                        'profession'        => $request->profession,
                        'address'           => $request->address,
                        'start_date'        => $request->start_date,
                        'end_date'          => $request->end_date,
                        'diagnosis'         => $request->diagnosis,
                        'hospital_id'       => auth()->user()->hospital_id,
                        'created_by'        => auth()->user()->id,
                    ]);

                    $nomor->update([
                        'auto_no_surat' => $autoNomor + 1
                    ]);

                    $sickLetterDownloadRoute = route('client.sick-letter.download', Crypt::encryptString($sickLetter->id));

                    Mail::to($sickLetter->patient_email)->send(new SickLetterMail($sickLetter, $sickLetterDownloadRoute));

                    DB::commit();
                    return response()->json([
                        'status'  => 200,
                        'message' => 'Sick letter has been created',
                    ], 200);
                } catch (Throwable $th) {
                    DB::rollBack();
                    return response()->json([
                        'status'  => 500,
                        'message' => $th->getMessage(),
                    ], 500);
                }
            }
        } else {
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SickLetter $sickLetter)
    {
        abort(403);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = auth()->user();

        if ($user->can('update sick letter')) {
            try {
                $id = Crypt::decryptString($id);
                $data = SickLetter::find($id);

                if ($data) {
                    return response()->json([
                        'status'    => 200,
                        'data'      => $data,
                    ]);
                } else {
                    return response()->json([
                        'status'    => 404,
                        'message'   => 'Data Not Found',
                    ]);
                }
            } catch (\Throwable $e) {
                return response()->json([
                    'status'  => 500,
                    'message' => "Error on line {$e->getLine()}: {$e->getMessage()}",
                ], 500);
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

        if ($user->can('update sick letter')) {
            $data = SickLetter::find($id);

            $validator = Validator::make([
                'name'              => $request->name,
                'patient_email'     => $request->patient_email,
                'age'               => $request->age,
                'gender'            => $request->gender,
                'profession'        => $request->profession,
                'address'           => $request->address,
                'start_date'        => $request->start_date,
                'end_date'          => $request->end_date,
                'diagnosis'         => $request->diagnosis
            ], [
                'name'              => 'required|min:5',
                'patient_email'     => 'required',
                'age'               => 'required',
                'gender'            => 'required',
                'address'           => 'required',
                'start_date'        => 'required',
                'end_date'          => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->messages(),
                ]);
            } else {
                if ($data) {
                    DB::beginTransaction();

                    try {
                        $data->update([
                            'patient_name'      => $request->name,
                            'patient_email'     => $request->patient_email,
                            'age'               => $request->age,
                            'gender'            => $request->gender,
                            'profession'        => $request->profession,
                            'address'           => $request->address,
                            'start_date'        => $request->start_date,
                            'end_date'          => $request->end_date,
                            'diagnosis'         => $request->diagnosis,
                        ]);

                        DB::commit();

                        return response()->json([
                            'status'  => 200,
                            'message' => 'Sick letter has been updated',
                        ], 200);
                    } catch (\Throwable $th) {
                        DB::rollBack();
                        return response()->json([
                            'status'  => 500,
                            'message' => $th->getMessage(),
                        ], 500);
                    }
                } else {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Data not found..!',
                    ]);
                }
            }
        } else {
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SickLetter $sickLetter)
    {
        abort(403);
    }

    public function printPdf($id)
    {
        $id = Crypt::decryptString($id);
        $data = SickLetter::with([
            'user' => function ($query) {
                return $query->select('id', 'name');
            },
            'hospital' => function ($query) {
                return $query->select('id', 'name', 'logo');
            }
        ])->find($id);

        $startDate = $data->start_date;
        $endDate = $data->end_date;

        // Mengonversi string ke objek Carbon
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Menghitung jumlah hari
        $dayDifference = $start->diffInDays($end) + 1;

        $qrcodeRoute = route('client.sick-letter.sign', $data->slug);

        $qrCode = base64_encode(QrCode::format('svg')->size(85)->errorCorrection('H')->generate($qrcodeRoute));

        $pdf = PDF::loadHTML(view('admin.modules.sick-letter.sick-letter-pdf', compact('data', 'dayDifference', 'qrCode', 'qrcodeRoute')))
            ->setPaper('a4', 'potrait');

        return $pdf->stream();
    }

    public function qrSign($slug)
    {
        $data = SickLetter::where('slug', $slug)->first();

        return view('admin.modules.sick-letter.sign-status', compact('data'));
    }

    public function downloadPdf($id)
    {
        $id = Crypt::decryptString($id);
        $data = SickLetter::with([
            'user' => function ($query) {
                return $query->select('id', 'name');
            },
            'hospital' => function ($query) {
                return $query->select('id', 'name', 'logo');
            }
        ])->find($id);

        $startDate = $data->start_date;
        $endDate = $data->end_date;

        // Mengonversi string ke objek Carbon
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Menghitung jumlah hari
        $dayDifference = $start->diffInDays($end) + 1;

        $qrcodeRoute = route('client.sick-letter.sign', $data->slug);

        $qrCode = base64_encode(QrCode::format('svg')->size(85)->errorCorrection('H')->generate($qrcodeRoute));

        $pdf = PDF::loadHTML(view('admin.modules.sick-letter.sick-letter-pdf', compact('data', 'dayDifference', 'qrCode', 'qrcodeRoute')))
            ->setPaper('a4', 'potrait');

        return $pdf->download();
    }
}
