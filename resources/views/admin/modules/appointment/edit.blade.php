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
                        <form action="{{ route('admin.appointment.update', Crypt::encryptString($data->id)) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row form-row">
                                <div class="col-12 col-sm-9">
                                    <div class="form-group">
                                        <label>Patient Name <span class="text-danger">*</span></label>
                                        <input name="patient_name" type="text" class="form-control @error('patient_name') is-invalid @enderror" 
                                            value="{{ old('patient_name') ?? $data->patient_name }}">
                                        @error('patient_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
        
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>Date Of Birth <span class="text-danger">*</span></label>
                                        <input name="dob" type="date" class="form-control @error('dob') is-invalid @enderror" 
                                            value="{{ old('dob') ?? $data->patient_dob }}">
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
                                        <select name="gender" id="gender" class="form-control select @error('gender') is-invalid @enderror">
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
                                            value="{{ old('phone') ?? $data->patient_telp }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-5">
                                    <div class="form-group">
                                        <label>Email <span class="text-danger">*</span></label>
                                        <input name="email" type="text" class="form-control @error('email') is-invalid @enderror" 
                                            value="{{ old('email') ?? $data->patient_email }}">
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
                                            value="{{ old('address') ?? $data->patient_address }}">
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <button name="btnSimpan" class="btn btn-primary" type="submit" id="btnSave">{{ $btnSubmit }}</button>
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
    });
</script>
@endpush