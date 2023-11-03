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
                    <li class="breadcrumb-item">Doctors</li>
                    <li class="breadcrumb-item">Practice Locations</li>
                    <li class="breadcrumb-item active">{{ $breadcrumb }}</li>
                </ul>
            </div>
            <div class="col-sm-5 col">
                <a href="{{ route('admin.doctor-location.index') }}" class="btn btn-secondary float-right mt-2" type="button">
                    Back
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 mt-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row form-row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label> Doctor Name <span class="text-danger">*</span></label>
                                    <select name="doctor" class="select @error('doctor') is-invalid @enderror" disabled>
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
                                    <label> Hospital / Clinic Name <span class="text-danger">*</span></label>
                                    <select name="hospital" class="select @error('hospital') is-invalid @enderror" disabled>
                                        <option selected disabled>-- Please Selected --</option>
                                        @foreach ($hospitals as $hospital)
                                        <option value="{{ $hospital->id }}"
                                            {{ old('hospital', $data->hospital_id) == $hospital->id ? 'selected' : null }}>{{ $hospital->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('hospital')
                                        <span class="text-danger" style="margin-top: .25rem; font-size: 80%;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row form-row" id="item-form">
                            <div class="form-group col-12 col-md-11">
                                <div class="table-responsive">
                                    <table id="tb-item" class="table table-stripped" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Day</th>
                                                <th>Start Time</th>
                                                <th>End Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($days as $index => $day)
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control @error('day') is-invalid @enderror" name="day[]" id="day" placeholder="Example : MON - FRI" value="{{ old('day') ?? $day->day }}" style='text-transform:uppercase' disabled>
                                                            @error('day')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </td>
                                                        <td>
                                                            <input type="time" class="form-control @error('start_time') is-invalid @enderror" name="start_time[]" id="time" value="{{ old('start_time') ?? $day->start_time }}" disabled>
                                                            @error('start_time')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </td>
                                                        <td>
                                                            <input type="time" class="form-control @error('end_time') is-invalid @enderror" name="end_time[]" id="time" value="{{ old('end_time') ?? $day->end_time }}" disabled>
                                                            @error('end_time')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </td>
                                                    </tr>
                                                @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
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