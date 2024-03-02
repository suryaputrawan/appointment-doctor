@extends('master.client.layout.app')

@push('plugin-style')
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/client/plugins/select2/css/select2.min.css') }}"> 
@endpush

@section('breadcrumb')
    <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-12 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('client.home') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb }}</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">{{ $breadcrumb }}</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- /Breadcrumb -->
@endsection

@section('content')
    <div class="content">
        <div class="container">
        
            <div class="row">
                <div class="col-12">
                
                    <div class="card">
                        <div class="card-body">
                            <div class="booking-doc-info">
                                <a href="{{ route('client.doctor.show', $data->slug) }}" class="booking-doc-img">
                                    <img src="{{ $data->takePicture }}" alt="User Image">
                                </a>
                                <div class="booking-info">
                                    <h4>
                                        <a href="{{ route('client.doctor.show', $data->slug) }}" class="mb-0">{{ $data->name }}</a>
                                        <p class="speciality" style="font-size: 12px;">{{ $data->specialization }} - {{ $data->speciality->name }}</p>
                                    </h4>
                                    <p class="text-muted mb-0" style="font-size: 12px;"><i class="fas fa-map-marker-alt"></i>
                                    @foreach ($data->doctorLocation as $index => $location)
                                        @if ($index == 0)
                                            {{ $location->hospital->name }}
                                        @else
                                            , {{ $location->hospital->name }}
                                        @endif
                                    @endforeach
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('client.appointment.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" id="doctor" name="doctor" value="{{ $data->id }}">

                                <!-- Personal Information -->
                                <div class="info-widget">
                                    <h4 class="card-title">Personal Information</h4>
                                    <div class="row">
                                        <div class="col-md-9 col-sm-12">
                                            <div class="form-group card-label">
                                                <label>Full Name <span class="text-danger">*</span></label>
                                                <input name="fullname" class="form-control @error('fullname') is-invalid @enderror" type="text" value="{{ old('fullname') }}">
                                                @error('fullname')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-sm-12">
                                            <div class="form-group card-label">
                                                <label>Date Of Birth / Tanggal Lahir <span class="text-danger">*</span></label>
                                                <input name="dob" class="form-control @error('dob') is-invalid @enderror" type="date" value="{{ old('dob') }}">
                                                @error('dob')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3 col-sm-12">
                                            <div class="form-group card-label">
                                                <label>Sex <span class="text-danger">*</span></label>
                                                <select name="sex" id="sex" class="select @error('sex') is-invalid @enderror">
                                                    <option selected disabled>Select</option>
                                                    <option value="M"
                                                        {{ old('sex') == "M" ? 'selected' : null }}>Male
                                                    </option>
                                                    <option value="F"
                                                    {{ old('sex') == "F" ? 'selected' : null }}>Female
                                                </option>
                                                </select>
                                                @error('sex')
                                                    <span class="text-danger" style="margin-top: .25rem; font-size: 80%;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group card-label mt-2">
                                                <label>Phone <span class="text-danger">*</span></label>
                                                <input name="phone" class="form-control @error('phone') is-invalid @enderror" type="text" value="{{ old('phone') }}">
                                                @error('phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-5 col-sm-12">
                                            <div class="form-group card-label mt-2">
                                                <label>Email <span class="text-danger">*</span></label>
                                                <input name="email" class="form-control @error('email') is-invalid @enderror" type="text" value="{{ old('email') }}">
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 col-sm-12">
                                            <div class="form-group card-label">
                                                <label>Address <span class="text-danger">*</span></label>
                                                <input name="address" class="form-control @error('address') is-invalid @enderror" type="text" value="{{ old('address') }}">
                                                @error('address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 col-sm-12">
                                            <div class="form-group card-label">
                                                <label>Symptoms / Notes <span class="text-danger">*</span></label>
                                                <input name="symptoms" class="form-control @error('symptoms') is-invalid @enderror" type="text" value="{{ old('symptoms') }}">
                                                @error('symptoms')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <span class="text-info" style="margin-top: .25rem; font-size: 80%;">Input the symptoms you are experiencing, or your purpose in meet with the doctor</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Personal Information -->

                                <!-- Booking Information -->
                                <div class="info-widget">
                                    <h4 class="card-title">Booking Information</h4>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12">
                                            <div class="form-group card-label">
                                                <label>Clinic <span class="text-danger">*</span></label>
                                                <select name="hospital" id="hospital" class="select @error('hospital') is-invalid @enderror">
                                                    <option selected disabled>Select</option>
                                                    @foreach ($hospitals as $item)
                                                    <option value="{{ $item->hospital_id }}"
                                                        {{ old('hospital') == $item->hospital_id ? 'selected' : null }}>{{ $item->hospital->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('hospital')
                                                    <span class="text-danger" style="margin-top: .25rem; font-size: 80%;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 col-sm-12">
                                            <input type="hidden" id="booking-day" name="booking_day">
                                            <input type="hidden" id="booking-day-date" name="booking_day_date">
                                            <div class="form-group card-label">
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
                                        <div class="col-md-6 col-sm-12">
                                            <input type="hidden" id="booking-start-time" name="booking_start_time">
                                            <input type="hidden" id="booking-end-time" name="booking_end_time">
                                            <div class="form-group card-label" id="time-form">
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
                                <!-- /Booking Information -->

                                <div class="text-right">   
                                    <button class="btn btn-primary" type="submit" id="btnSave">{{ $btnSubmit }}</button>
                                    <button class="btn btn-primary" type="submit" id="btnSave-loading" style="display: none">
                                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                        <span>{{ $btnSubmit }}</span>
                                    </button>
                                    <a href="{{ route('client.home') }}" class="btn btn-danger" id="btnCancel" type="button">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>	
@endsection

@push('script')
    <!-- Fancybox JS -->
    <script src="{{ asset('assets/client/plugins/fancybox/jquery.fancybox.min.js') }}"></script>
    <!-- Select2 JS -->
    <script src="{{ asset('assets/client/plugins/select2/js/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('custom-script')
<script type="text/javascript">
    $(document).ready(function () {

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
            let id_date     = $('#booking-day-date').val();
            $('#info-time').text('');
            $('#booking-time').html('<option></option>');

            var bookingDay = $('#booking-day').val();

            // loadTimeData(id_hospital, id_doctor, id_date, null, true);
            loadWaktuDokter(id_hospital, id_doctor, id_date, bookingDay, null, true);
            
            $('#booking-start-time').val("");
            $('#booking-end-time').val("");
        });

        $('#booking-time').on('change', function() {
            var selectedIndex = document.getElementById('booking-time').value;
            var selectedOption = document.getElementById('booking-time').options[selectedIndex];
            var startTime = selectedOption.getAttribute('data-start-time');
            var endTime = selectedOption.getAttribute('data-end-time');

            $('#booking-start-time').val(startTime);
            $('#booking-end-time').val(endTime);
        });

        @if (old('hospital'))
            let id_hospital = $('#hospital').val();
            let id_doctor   = $('#doctor').val();

            loadJadwalDokter(id_hospital, id_doctor, null, true);
        @endif

        //--Environtment Button
        $('#btnSave').on('click', function () {
            $('#btnSave-loading').toggle();
            $('#btnSave-loading').prop('disabled',true);
            $('#btnSave').toggle();
            $('#btnCancel').toggle();
        });

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

        @if (session('error')) {
            Toast.fire({
                icon: 'warning',
                title: "{{ session('error') }}",
            });
        }
        @endif
    });
</script>
@endpush