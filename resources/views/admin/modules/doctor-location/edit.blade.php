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
                        @if(session()->has('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                {{ session()->get('error') }}
                            </div>
                            @php
                                Session::forget('error');
                            @endphp
                        @endif
                        <form action="{{ route('admin.doctor-location.update', Crypt::encryptString($data->id)) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row form-row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label> Doctor Name <span class="text-danger">*</span></label>
                                        <select name="doctor" class="select @error('doctor') is-invalid @enderror">
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
                                        <select name="hospital" class="select @error('hospital') is-invalid @enderror">
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
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (old('day') || old('start_time') || old('end_time'))
                                                    @for ($i = 0; $i < count(old('day')); $i++)
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control @error('day.'.$i) is-invalid @enderror" name="day[]" id="day" placeholder="Example : MON - FRI" value="{{ old('day.'.$i) }}" style='text-transform:uppercase' autocomplete="off">
                                                                @error('day.'.$i)
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </td>
                                                            <td>
                                                                <input type="time" class="form-control @error('start_time.'.$i) is-invalid @enderror" name="start_time[]" id="time" value="{{ old('start_time.'.$i) }}" autocomplete="off">
                                                                @error('start_time.'.$i)
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </td>
                                                            <td>
                                                                <input type="time" class="form-control @error('end_time.'.$i) is-invalid @enderror" name="end_time[]" id="time" value="{{ old('end_time.'.$i) }}" autocomplete="off">
                                                                @error('end_time.'.$i)
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </td>
                                                            <td>
                                                                <button id="btn-item-delete" type="button" class="btn btn-danger waves-effect waves-light"><i class='fe fe-trash'></i></button>
                                                            </td>
                                                        </tr>
                                                    @endfor 
                                                @else
                                                    @foreach ($days as $index => $day)
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control @error('day') is-invalid @enderror" name="day[]" id="day" placeholder="Example : MON - FRI" value="{{ old('day') ?? $day->day }}" style='text-transform:uppercase' autocomplete="off">
                                                                @error('day')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </td>
                                                            <td>
                                                                <input type="time" class="form-control @error('start_time') is-invalid @enderror" name="start_time[]" id="time" value="{{ old('start_time') ?? $day->start_time }}" autocomplete="off">
                                                                @error('start_time')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </td>
                                                            <td>
                                                                <input type="time" class="form-control @error('end_time') is-invalid @enderror" name="end_time[]" id="time" value="{{ old('end_time') ?? $day->end_time }}" autocomplete="off">
                                                                @error('end_time')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </td>
                                                            <td>
                                                                <button id="btn-item-delete" type="button" class="btn btn-danger waves-effect waves-light"><i class='fe fe-trash'></i></button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="form-group col-12 col-md-1">
                                    <button id="btn-item-add" type="button" class="btn btn-sm btn-primary"><i class='fe fe-plus'></i></button>
                                </div>
                            </div>

                            <div class="text-right">
                                <button name="btnSimpan" class="btn btn-primary" type="submit" id="btnSave">{{ $btnSubmit }}</button>
                                <button class="btn btn-primary" type="submit" id="btnSave-loading" style="display: none">
                                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    <span>{{ $btnSubmit }}</span>
                                </button>
                                <a href="{{ route('admin.doctor-location.index') }}" class="btn btn-danger">Cancel</a>
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
    let item = "<tr>"+
                    "<td>"+
                        "<input type='text' class='form-control' name='day[]' placeholder='Example : MON - FRI' style='text-transform:uppercase' value='{{ old('day.*') }}' autocomplete='off'>"+
                        "<p id='error-day' style='color: red' class='error'></p>"+
                    "</td>"+
                    "<td>"+
                        "<input type='time' class='form-control' name='start_time[]' value='{{ old('start_time.*') }}' autocomplete='off'>"+
                        "<p id='error-start_time' style='color: red' class='error'></p>"+
                    "</td>"+
                    "<td>"+
                        "<input type='time' class='form-control' name='end_time[]' value='{{ old('end_time.*') }}' autocomplete='off'>"+
                        "<p id='error-end_time' style='color: red' class='error'></p>"+
                    "</td>"+
                    "<td>"+
                        "<button id='btn-item-delete' type='button' class='btn btn-sm btn-danger'><i class='fe fe-trash'></i></button>"+
                    "</td>"+
                "</tr>"
    //--Repeat item form

    $(document).ready(function() {
        $('#btn-item-add').click(function() {
            $('#tb-item > tbody').append(item);
        });

        $('tbody').on('click','#btn-item-delete', function() {
            $(this).parent().parent().remove();
        });
    });
    //--- End repeat item form

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
    });
</script>
@endpush