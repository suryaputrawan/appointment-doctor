@extends('master.admin.layout.app', ['title' => 'Doctor Educations'])

@push('plugin-style')
    <!-- Datatables CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables/datatables.min.css') }}">
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/select2.min.css') }}">
@endpush

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-7 col-auto">
                <h3 class="page-title">{{ $pageTitle }}</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Appointment</li>
                    <li class="breadcrumb-item active">{{ $breadcrumb }}</li>
                </ul>
            </div>
            <div class="col-sm-5 col">
                <a href="{{ route('admin.appointment.index') }}" class="btn btn-secondary float-right mt-2" type="button">
                    Back
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 mt-3">
                <div class="card">
                    <div class="card-body">
                        @if(session()->has('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                {{ session()->get('error') }}
                            </div>
                            @php
                                Session::forget('error');
                            @endphp
                        @endif
                        <form action="{{ route('admin.appointment.rescheduleUpdate', Crypt::encryptString($data->id)) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row form-row">
                                <div class="col-12 col-sm-9">
                                    <div class="form-group">
                                        <label>Patient Name <span class="text-danger">*</span></label>
                                        <input name="patient_name" type="text" class="form-control @error('patient_name') is-invalid @enderror" 
                                            value="{{ old('patient_name') ?? $data->patient_name }}" disabled>
                                        @error('patient_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
        
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>Date Of Birth <span class="text-danger">*</span></label>
                                        <input name="dob" type="date" class="form-control @error('dob') is-invalid @enderror" 
                                            value="{{ old('dob') ?? $data->patient_dob }}" disabled>
                                        @error('dob')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row form-row">
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>Gender <span class="text-danger">*</span></label>
                                        <select name="gender" id="gender" class="form-control select @error('gender') is-invalid @enderror" disabled>
                                            <option selected disabled>-- Please Selected --</option>
                                            <option value="M" {{ old('gender', $data->patient_sex) == "M" ? 'selected' : null }}>MALE</option>
                                            <option value="F" {{ old('gender', $data->patient_sex) == "F" ? 'selected' : null }}>FEMALE</option>
                                        </select>
                                        @error('gender')
                                            <span class="text-danger" style="margin-top: .25rem; font-size: 80%;">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label>Phone <span class="text-danger">*</span></label>
                                        <input name="phone" type="text" class="form-control @error('phone') is-invalid @enderror" 
                                            value="{{ old('phone') ?? $data->patient_telp }}" disabled>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-5">
                                    <div class="form-group">
                                        <label>Email <span class="text-danger">*</span></label>
                                        <input name="email" type="text" class="form-control @error('email') is-invalid @enderror" 
                                            value="{{ old('email') ?? $data->patient_email }}" disabled>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
        
                            <div class="row form-row">
                                <div class="col-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Address <span class="text-danger">*</span></label>
                                        <input name="address" type="text" class="form-control @error('address') is-invalid @enderror" 
                                            value="{{ old('address') ?? $data->patient_address }}" disabled>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
        
                            <hr>

                            <div class="row form-row">
                                <div class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label>Time type <span class="text-danger">*</span></label>
                                        <select name="time_type" id="time-type" class="form-control select @error('time_type') is-invalid @enderror">d
                                            <option value="schedule"
                                                {{ old('time_type', $data->time_type) == 'schedule' ? 'selected' : null }}>Schedule Time
                                            </option>
                                            <option value="manual"
                                                {{ old('time_type', $data->time_type) == 'manual' ? 'selected' : null }}>Manual Time
                                            </option>
                                        </select>
                                        @error('time_type')
                                            <span class="text-danger" style="margin-top: .25rem; font-size: 80%;">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            @if ($data->time_type == "schedule")
                                <div id="appointment-schedule">
                                    <div class="row form-row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Doctor Name <span class="text-danger">*</span></label>
                                                <select name="doctor" id="doctor" class="form-control select @error('doctor') is-invalid @enderror">
                                                    <option selected disabled>-- Please Selected --</option>
                                                    @foreach ($doctors as $doctor)
                                                    <option value="{{ $doctor->id }}"
                                                        {{ old('doctor', $data->doctor_id) == $doctor->id ? 'selected' : null }}>{{ $doctor->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('doctor')
                                                    <span class="text-danger" style="margin-top: .25rem; font-size: 80%;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Clinic <span class="text-danger">*</span></label>
                                                <select name="hospital" id="hospital" class="form-control select @error('hospital') is-invalid @enderror" data-width="100%">
                                                    <option value="{{ $data->hospital_id }}">{{ $data->hospital->name }}</option>
                                                </select>
                                                @error('hospital')
                                                    <span class="text-danger" style="margin-top: .25rem; font-size: 80%;">{{ $message }}</span>
                                                @enderror
                                                <div id="loading-hospital" style="display: none">
                                                    <div class="d-flex align-items-center">
                                                        <div class="spinner-border text-primary spinner-border-sm mr-2" role="status" aria-hidden="true"></div>
                                                        <p>Please wait...</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
        
                                    <div class="row">
                                        <div class="col-md-3 col-sm-12">
                                            <input type="hidden" id="booking-day">
                                            <input type="hidden" id="booking-day-date" name="booking_day_date">
                                            <div class="form-group">
                                                <label>Date <span class="text-danger">*</span></label>
                                                <select name="booking_date" id="booking-date" class="form-control select @error('booking_date') is-invalid @enderror" data-width="100%">
                                                    <option value="{{ $data->date }}">{{ \Carbon\Carbon::parse($data->date)->format('d M Y') }}</option>
                                                </select>
                                                @error('booking_date')
                                                    <span class="text-danger" style="margin-top: .25rem; font-size: 80%;">{{ $message }}</span>
                                                @enderror
                                                <div id="loading-date" style="display: none">
                                                    <div class="d-flex align-items-center">
                                                        <div class="spinner-border text-primary spinner-border-sm mr-2" role="status" aria-hidden="true"></div>
                                                        <p>Please wait...</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-12">
                                            <div class="form-group">
                                                <input type="hidden" id="booking-start-time" name="booking_start_time">
                                                <input type="hidden" id="booking-end-time" name="booking_end_time">
                                                <label>Time <span class="text-danger">*</span></label>
                                                <select name="booking_time" id="booking-time" class="form-control select @error('booking_time') is-invalid @enderror" data-width="100%">
                                                    {{-- <option value="{{ $schedule->id }}">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }} Wita</option> --}}
                                                    <option>{{ \Carbon\Carbon::parse($data->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($data->end_time)->format('H:i') }} Wita</option>
                                                </select>
                                                @error('booking_time')
                                                    <span class="text-danger" style="margin-top: .25rem; font-size: 80%;">{{ $message }}</span>
                                                @enderror
                                                <span class="text-danger" id="info-time"></span>
                                                <div id="loading-time" style="display: none">
                                                    <div class="d-flex align-items-center">
                                                        <div class="spinner-border text-primary spinner-border-sm mr-2" role="status" aria-hidden="true"></div>
                                                        <p>Please wait...</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="appointment-manual" style="display: none">
                                    <div class="row form-row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Doctor Name <span class="text-danger">*</span></label>
                                                <select name="doctor_name" id="doctor-name" class="form-control select @error('doctor_name') is-invalid @enderror">
                                                    <option value="">-- Please Selected --</option>
                                                    @foreach ($doctors as $doctor)
                                                    <option value="{{ $doctor->id }}"
                                                        {{ old('doctor_name', $data->doctor_id) == $doctor->id ? 'selected' : null }}>{{ $doctor->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('doctor_name')
                                                    <span class="text-danger" style="margin-top: .25rem; font-size: 80%;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Clinic <span class="text-danger">*</span></label>
                                                <select name="clinic_name" id="clinic-name" class="form-control select @error('clinic_name') is-invalid @enderror" data-width="100%">
                                                    <option value="">Please Selected</option>
                                                    @foreach ($hospitals as $hospital)
                                                    <option value="{{ $hospital->id }}"
                                                        {{ old('clinic_name', $data->hospital_id) == $hospital->id ? 'selected' : null }}>{{ $hospital->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('clinic_name')
                                                    <span class="text-danger" style="margin-top: .25rem; font-size: 80%;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                
                                    <div class="row">
                                        <div class="col-md-3 col-sm-12">
                                            <div class="form-group">
                                                <label>Date <span class="text-danger">*</span></label>
                                                <input name="date_appointment" id="date-appointment" type="date" class="form-control @error('date_appointment') is-invalid @enderror" value="{{ old('date_appointment') ?? $data->date }}">
                                                @error('date_appointment')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-12">
                                            <div class="form-group">
                                                <label>Time <span class="text-danger">*</span></label>
                                                <input name="time_appointment" id="time-appointment" type="time" class="form-control @error('time_appointment') is-invalid @enderror" value="{{ old('time_appointment') ?? $data->start_time }}">
                                                @error('time_appointment')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else 
                                <div id="appointment-schedule" style="display: none">
                                    <div class="row form-row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Doctor Name <span class="text-danger">*</span></label>
                                                <select name="doctor" id="doctor" class="form-control select @error('doctor') is-invalid @enderror">
                                                    <option value="">-- Please Selected --</option>
                                                    @foreach ($doctors as $doctor)
                                                    <option value="{{ $doctor->id }}"
                                                        {{ old('doctor') == $doctor->id ? 'selected' : null }}>{{ $doctor->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('doctor')
                                                    <span class="text-danger" style="margin-top: .25rem; font-size: 80%;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Clinic <span class="text-danger">*</span></label>
                                                <select name="hospital" id="hospital" class="form-control select @error('hospital') is-invalid @enderror" data-width="100%">
                                                    <option selected disabled></option>
                                                </select>
                                                @error('hospital')
                                                    <span class="text-danger" style="margin-top: .25rem; font-size: 80%;">{{ $message }}</span>
                                                @enderror
                                                <div id="loading-hospital" style="display: none">
                                                    <div class="d-flex align-items-center">
                                                        <div class="spinner-border text-primary spinner-border-sm mr-2" role="status" aria-hidden="true"></div>
                                                        <p>Please wait...</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                
                                    <div class="row">
                                        <div class="col-md-4 col-sm-12">
                                            <input type="hidden" id="booking-day">
                                            <input type="hidden" id="booking-day-date" name="booking_day_date">
                                            <div class="form-group">
                                                <label>Date <span class="text-danger">*</span></label>
                                                <select name="booking_date" id="booking-date" class="form-control select @error('booking_date') is-invalid @enderror" data-width="100%">
                                                    <option selected disabled></option>
                                                </select>
                                                @error('booking_date')
                                                    <span class="text-danger" style="margin-top: .25rem; font-size: 80%;">{{ $message }}</span>
                                                @enderror
                                                <div id="loading-date" style="display: none">
                                                    <div class="d-flex align-items-center">
                                                        <div class="spinner-border text-primary spinner-border-sm mr-2" role="status" aria-hidden="true"></div>
                                                        <p>Please wait...</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-12">
                                            <input type="hidden" id="booking-start-time" name="booking_start_time">
                                            <input type="hidden" id="booking-end-time" name="booking_end_time">
                                            <div class="form-group">
                                                <label>Time <span class="text-danger">*</span></label>
                                                <select name="booking_time" id="booking-time" class="form-control select @error('booking_time') is-invalid @enderror" data-width="100%">
                                                    <option selected disabled></option>
                                                </select>
                                                @error('booking_time')
                                                    <span class="text-danger" style="margin-top: .25rem; font-size: 80%;">{{ $message }}</span>
                                                @enderror
                                                <span class="text-danger" id="info-time"></span>
                                                <div id="loading-time" style="display: none">
                                                    <div class="d-flex align-items-center">
                                                        <div class="spinner-border text-primary spinner-border-sm mr-2" role="status" aria-hidden="true"></div>
                                                        <p>Please wait...</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="appointment-manual">
                                    <div class="row form-row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Doctor Name <span class="text-danger">*</span></label>
                                                <select name="doctor_name" id="doctor-name" class="form-control select @error('doctor_name') is-invalid @enderror">
                                                    <option value="">-- Please Selected --</option>
                                                    @foreach ($doctors as $doctor)
                                                    <option value="{{ $doctor->id }}"
                                                        {{ old('doctor_name', $data->doctor_id) == $doctor->id ? 'selected' : null }}>{{ $doctor->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('doctor_name')
                                                    <span class="text-danger" style="margin-top: .25rem; font-size: 80%;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Clinic <span class="text-danger">*</span></label>
                                                <select name="clinic_name" id="clinic-name" class="form-control select @error('clinic_name') is-invalid @enderror" data-width="100%">
                                                    <option value="">Please Selected</option>
                                                    @foreach ($hospitals as $hospital)
                                                    <option value="{{ $hospital->id }}"
                                                        {{ old('clinic_name', $data->hospital_id) == $hospital->id ? 'selected' : null }}>{{ $hospital->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('clinic_name')
                                                    <span class="text-danger" style="margin-top: .25rem; font-size: 80%;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                
                                    <div class="row">
                                        <div class="col-md-3 col-sm-12">
                                            <div class="form-group">
                                                <label>Date <span class="text-danger">*</span></label>
                                                <input name="date_appointment" id="date-appointment" type="date" class="form-control @error('date_appointment') is-invalid @enderror" value="{{ old('date_appointment') ?? $data->date }}">
                                                @error('date_appointment')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-12">
                                            <div class="form-group">
                                                <label>Time <span class="text-danger">*</span></label>
                                                <input name="time_appointment" id="time-appointment" type="time" class="form-control @error('time_appointment') is-invalid @enderror" value="{{ old('time_appointment') ?? $data->start_time }}">
                                                @error('time_appointment')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="text-right">
                                <button name="btnSimpan" class="btn btn-primary" type="submit" id="btnSave" style="display: none">{{ $btnSubmit }}</button>
                                <button class="btn btn-primary" type="submit" id="btnSave-loading" style="display: none">
                                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    <span>{{ $btnSubmit }}</span>
                                </button>
                                <a href="{{ route('admin.appointment.index') }}" class="btn btn-danger" id="btnCancel">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>			
        </div>
    </div>
@endsection

@push('plugin-script')
    <!-- Datatables JS -->
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('custom-scripts')
<script type="text/javascript">
    $(document).ready(function() {

        //-- Get data hospital
        function loadHospitalData(id_doctor, selected, replaceChild) {
            $('#loading-hospital').show();

            let url = "{{ route('client.getBookingHospital') }}";

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    id_doctor: id_doctor,
                    selected: selected
                },
                cache: false,

                success: function (response) {
                    $('#loading-hospital').hide();
                    if (response.status == 404) {
                        Toast.fire({
                            icon: 'warning',
                            title: response.message,
                        });
                    } else {
                        $('#hospital').html(response.data);
                        if (replaceChild == true) {
                            $('#booking-date').html('<option></option>');
                        }
                    }
                },
                error: function (response) {
                    $('#loading-hospital').hide();
                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.message ?? 'Oops,.. Something went wrong!',
                    });
                }
            });
        }
        //-- End get data hospital

        //-- Get data date
        // function loadDateData(id_hospital, id_doctor, selected, replaceChild) {

        //     let url = "{{ route('client.getBookingDate') }}";

        //     const Toast = Swal.mixin({
        //         toast: true,
        //         position: 'top-end',
        //         showConfirmButton: false,
        //         timer: 3000,
        //         timerProgressBar: true,
        //         didOpen: (toast) => {
        //             toast.addEventListener('mouseenter', Swal.stopTimer)
        //             toast.addEventListener('mouseleave', Swal.resumeTimer)
        //         }
        //     });

        //     $.ajax({
        //         type: 'GET',
        //         url: url,
        //         data: {
        //             id_hospital: id_hospital,
        //             id_doctor: id_doctor,
        //             selected: selected
        //         },
        //         cache: false,

        //         success: function (response) {

        //             if (response.status == 404) {
        //                 Toast.fire({
        //                     icon: 'warning',
        //                     title: response.message,
        //                 });
        //             } else {
        //                 $('#booking-date').html(response.data);
        //                 if (replaceChild == true) {
        //                     $('#booking-time').html('<option></option>');
        //                 }
        //             }
        //         },
        //         error: function (response) {
        //             Toast.fire({
        //                 icon: 'error',
        //                 title: response.responseJSON.message ?? 'Oops,.. Something went wrong!',
        //             });
        //         }
        //     });
        // }
        //-- End get data date

        //-- Get data time
        // function loadTimeData(id_hospital, id_doctor, id_date, selected, replaceChild) {

        //     let url = "{{ route('client.getBookingTime') }}";

        //     const Toast = Swal.mixin({
        //         toast: true,
        //         position: 'top-end',
        //         showConfirmButton: false,
        //         timer: 3000,
        //         timerProgressBar: true,
        //         didOpen: (toast) => {
        //             toast.addEventListener('mouseenter', Swal.stopTimer)
        //             toast.addEventListener('mouseleave', Swal.resumeTimer)
        //         }
        //     });

        //     $.ajax({
        //         type: 'GET',
        //         url: url,
        //         data: {
        //             id_hospital: id_hospital,
        //             id_doctor: id_doctor,
        //             id_date: id_date,
        //             selected: selected
        //         },
        //         cache: false,

        //         success: function (response) {

        //             if (response.status == 404) {
        //                 Toast.fire({
        //                     icon: 'warning',
        //                     title: response.message,
        //                 });
        //             } else if (response.status == 201) {
        //                 $('#item-form').toggle();
        //                 $('#info-item-form').toggle();
        //                 $('#info-time').text(response.message);
        //             } else {
        //                 $('#booking-time').html(response.data);
        //             } 
        //         },
        //         error: function (response) {
        //             Toast.fire({
        //                 icon: 'error',
        //                 title: response.responseJSON.message ?? 'Oops,.. Something went wrong!',
        //             });
        //         }
        //     });
        // }
        //-- End get data time

        function loadJadwalDokter(id_hospital, id_doctor, selected, replaceChild) {
            $('#loading-date').show();

            let url = "{{ route('client.getJadwalDokter') }}";

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    id_hospital: id_hospital,
                    id_doctor: id_doctor,
                    selected: selected
                },
                cache: false,

                success: function (response) {
                    $('#loading-date').hide();
                    if (response.status == 404) {
                        Toast.fire({
                            icon: 'warning',
                            title: response.message,
                        });
                    } else {
                        $('#booking-date').html(response.data);
                        if (replaceChild == true) {
                            $('#booking-time').html('<option></option>');
                        }
                    }
                },
                error: function (response) {
                    $('#loading-date').hide();
                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.message ?? 'Oops,.. Something went wrong!',
                    });
                }
            });
        }

        function loadWaktuDokter(id_hospital, id_doctor, id_date, id_day, selected, replaceChild) {
            $('#loading-time').show();

            let url = "{{ route('client.getWaktuDokter') }}";

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    id_hospital: id_hospital,
                    id_doctor: id_doctor,
                    id_date: id_date,
                    id_day: id_day,
                    selected: selected
                },
                cache: false,

                success: function (response) {
                    $('#loading-time').hide();
                    if (response.status == 404) {
                        Toast.fire({
                            icon: 'warning',
                            title: response.message,
                        });
                    } else if (response.status == 201) {
                        $('#item-form').toggle();
                        $('#info-item-form').toggle();
                        $('#info-time').text(response.message);
                    } else {
                        $('#booking-time').html(response.data);
                    } 
                },
                error: function (response) {
                    $('#loading-time').hide();
                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.message ?? 'Oops,.. Something went wrong!',
                    });
                }
            });
        }

        //Dependent doctor dropdown
        $('#doctor').on('change', function() {
            let id_doctor   = $('#doctor').val();
            loadHospitalData(id_doctor, null, true);

            $('#booking-start-time').val("");
            $('#booking-end-time').val("");
            $('#booking-day').val("");
            $('#booking-day-date').val("");
            $('#booking-time').html('<option></option>');

            $('#btnSave').hide();
        });

        //Dependent hospital dropdown
        $('#hospital').on('change', function() {
            let id_hospital = $('#hospital').val();
            let id_doctor   = $('#doctor').val();
            // loadDateData(id_hospital, id_doctor, null, true);

            loadJadwalDokter(id_hospital, id_doctor, null, true);

            $('#booking-start-time').val("");
            $('#booking-end-time').val("");
            $('#booking-day').val("");
            $('#booking-day-date').val("");
            $('#booking-time').html('<option></option>');

            $('#btnSave').hide();
        });

        //Dependent date dropdown
        $('#booking-date').on('change', function() {
            var selectedIndex = document.getElementById('booking-date').value;
            var selectedOption = document.getElementById('booking-date').options[selectedIndex];
            var dayBooking = selectedOption.getAttribute('data-day');
            var dateBooking = selectedOption.getAttribute('data-date');

            $('#booking-day').val(dayBooking);
            $('#booking-day-date').val(dateBooking);

            let id_hospital = $('#hospital').val();
            let id_doctor   = $('#doctor').val();
            let id_date     = $('#booking-date').val();
            $('#info-time').text('');
            $('#booking-time').html('<option></option>');

            var bookingDay = $('#booking-day').val();

            // loadTimeData(id_hospital, id_doctor, id_date, null, true);
            loadWaktuDokter(id_hospital, id_doctor, id_date, bookingDay, null, true);
            
            $('#booking-start-time').val("");
            $('#booking-end-time').val("");

            $('#btnSave').hide();
        });

        $('#booking-time').on('change', function() {
            var selectedIndex = document.getElementById('booking-time').value;
            var selectedOption = document.getElementById('booking-time').options[selectedIndex];
            var startTime = selectedOption.getAttribute('data-start-time');
            var endTime = selectedOption.getAttribute('data-end-time');

            $('#booking-start-time').val(startTime);
            $('#booking-end-time').val(endTime);

            $('#btnSave').show();
        });

        @if (old('doctor'))
            let id_doctor   = $('#doctor').val();
            loadHospitalData(id_doctor, null, true);
        @endif

        //Toast for session success
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if (session('success')) {
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}",
            });
        }
        @endif

        //environment button
        $('#btnSave').on('click', function () {
            $('#btnSave-loading').toggle();
            $('#btnSave-loading').prop('disabled',true);
            $('#btnSave').toggle();
            $('#btnCancel').toggle();
        });
        //end

        // Fungsi Clear form time
        function clearFormTime()
        {
            $('#booking-start-time').val("");
            $('#booking-end-time').val("");
            $('#booking-day').val("");
            $('#booking-day-date').val("");

            $('#doctor').val('').trigger('change');
            $('#hospital').html('<option></option>');
            $('#booking-date').html('<option></option>');
            $('#booking-time').html('<option></option>');

            $('#doctor-name').val('').trigger('change');
            $('#clinic-name').val('').trigger('change');
            $('#date-appointment').val('');
            $('#time-appointment').val('');
        }

        // Melakukan perubahan saat field time type berubah
        $("#time-type").on('change', function () {
            if (this.value == "schedule") {
                $('#appointment-schedule').show(500);
                $('#appointment-manual').hide(500);

                clearFormTime();
            } else {
                $('#appointment-schedule').hide(500);
                $('#appointment-manual').show(500);

                clearFormTime();
            }
        });

        //Fungsi untuk mengecek dan menampilkan tombol save jika field telah terisi
        function checkFormInput() {
            var doctorName = $('#doctor-name').val();
            var clinicName = $('#clinic-name').val();
            var dateAppointment = $('#date-appointment').val();
            var timeAppointment = $('#time-appointment').val();

            if (doctorName !== '' && clinicName !== '' && dateAppointment !== '' && timeAppointment !== '') {
                $('#btnSave').show();
            } else {
                $('#btnSave').hide();
            }
        }

        // Memanggil fungsi saat field berubah
        $('#doctor-name, #clinic-name, #date-appointment, #time-appointment').on('change', function () {
            checkFormInput();
        });
    });
</script>
@endpush