@extends('master.admin.layout.app', ['title' => 'Doctor Educations'])

@push('plugin-style')
    <!-- Datatables CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables/datatables.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-7 col-auto">
                <h3 class="page-title">{{ $pageTitle }}</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Doctor Schedules</li>
                    <li class="breadcrumb-item active">{{ $breadcrumb }}</li>
                </ul>
            </div>
            <div class="col-sm-5 col">
                <a href="{{ route('admin.practice-schedules.index') }}" class="btn btn-secondary float-right mt-2" type="button">
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
                        <form action="{{ route('admin.practice-schedules.store') }}" method="POST">
                            @csrf
                            <div class="row form-row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label> Doctor Name <span class="text-danger">*</span></label>
                                        <select name="doctor" class="select @error('doctor') is-invalid @enderror">
                                            <option selected disabled>-- Please Selected --</option>
                                            @foreach ($doctor as $data)
                                            <option value="{{ $data->id }}"
                                                {{ old('doctor') == $data->id ? 'selected' : null }}>{{ $data->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('doctor')
                                            <span class="text-danger" style="margin-top: .25rem; font-size: 80%;">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label>Clinic Name <span class="text-danger">*</span></label>
                                        <select name="hospital" class="select @error('hospital') is-invalid @enderror">
                                            <option selected disabled>-- Please Selected --</option>
                                            @foreach ($hospital as $data)
                                            <option value="{{ $data->id }}"
                                                {{ old('hospital') == $data->id ? 'selected' : null }}>{{ $data->name }}
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
                                                    <th>Date</th>
                                                    <th>Start Time</th>
                                                    <th>End Time</th>
                                                    <th>Slot Duration</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (old('date') || old('start_time') || old('end_time'))
                                                    @for ($i = 0; $i < count(old('date')); $i++)
                                                        <tr>
                                                            <td>
                                                                <input type="date" class="form-control @error('date.'.$i) is-invalid @enderror" name="date[]" value="{{ old('date.'.$i) }}">
                                                                @error('date.'.$i)
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
                                                                <select name="duration[]" id="duration" class="select @error('duration') is-invalid @enderror" value="{{ old('duration.'.$i) }}">
                                                                    @foreach ($slotDuration as $data)
                                                                    <option value="{{ $data }}"
                                                                        {{ old('duration.'.$i) == $data ? 'selected' : null }}>{{ $data }} Minutes
                                                                    </option>
                                                                    @endforeach
                                                                </select>
                                                                @error('duration.'.$i)
                                                                    <span class="text-danger" style="margin-top: .25rem; font-size: 80%;">{{ $message }}</span>
                                                                @enderror
                                                            </td>
                                                            <td>
                                                                <button id="btn-item-delete" type="button" class="btn btn-danger waves-effect waves-light"><i class='fe fe-trash'></i></button>
                                                            </td>
                                                        </tr>
                                                    @endfor 
                                                    @else
                                                    <tr>
                                                        <td>
                                                            <input type="date" class="form-control @error('date') is-invalid @enderror" name="date[]" id="date" value="{{ old('date') }}" autocomplete="off">
                                                            @error('date')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </td>
                                                        <td>
                                                            <input type="time" class="form-control @error('start_time') is-invalid @enderror" name="start_time[]" id="timeInput" value="{{ old('start_time') }}" autocomplete="off">
                                                            @error('start_time')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </td>
                                                        <td>
                                                            <input type="time" class="form-control @error('end_time') is-invalid @enderror" name="end_time[]" id="time" value="{{ old('end_time') }}" autocomplete="off">
                                                            @error('end_time')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </td>
                                                        <td>
                                                            <select name="duration[]" id="duration" class="select @error('duration') is-invalid @enderror">
                                                                @foreach ($slotDuration as $data)
                                                                <option value="{{ $data }}"
                                                                    {{ old('duration') == $data ? 'selected' : null }}>{{ $data }} Minutes
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                            @error('duration')
                                                                <span class="text-danger" style="margin-top: .25rem; font-size: 80%;">{{ $message }}</span>
                                                            @enderror
                                                        </td>
                                                    </tr>
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
                                <button name="btnCreateSimpan" class="btn btn-warning" type="submit" id="btnCreateSave">Save & Create</button>
                                <button class="btn btn-warning" type="submit" id="btnCreateSave-loading" style="display: none">
                                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    <span>Save & Create</span>
                                </button>

                                <button name="btnSimpan" class="btn btn-primary" type="submit" id="btnSave">{{ $btnSubmit }}</button>
                                <button class="btn btn-primary" type="submit" id="btnSave-loading" style="display: none">
                                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    <span>{{ $btnSubmit }}</span>
                                </button>
                                <a href="{{ route('admin.practice-schedules.index') }}" class="btn btn-danger">Cancel</a>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('custom-scripts')
<script type="text/javascript">
    let item = "<tr>"+
                    "<td>"+
                        "<input type='date' class='form-control' name='date[]' value='{{ old('date.*') }}'>"+
                        "<p id='error-date' style='color: red' class='error'></p>"+
                    "</td>"+
                    "<td>"+
                        "<input type='time' class='form-control' name='start_time[]' value='{{ old('start_time.*') }}'>"+
                        "<p id='error-start_time' style='color: red' class='error'></p>"+
                    "</td>"+
                    "<td>"+
                        "<input type='time' class='form-control' name='end_time[]' value='{{ old('end_time.*') }}'>"+
                        "<p id='error-end_time' style='color: red' class='error'></p>"+
                    "</td>"+
                    "<td>"+
                        "<select class='select select-item @error('duration') is-invalid @enderror' name='duration[]'>"+
                            "@foreach ($slotDuration as $item)"+
                            "<option value='{{ $item }}'>{{ $item }} Minutes</option>"+
                            "@endforeach"+
                        "</select>"+
                        "<p id='error-duration' style='color: red' class='error'></p>"+
                    "</td>"+
                    "<td>"+
                        "<button id='btn-item-delete' type='button' class='btn btn-sm btn-danger'><i class='fe fe-trash'></i></button>"+
                    "</td>"+
                "</tr>"
    //--Repeat item form

    function selectRefresh() {
        $('.select-item').select2({
            tags: true,
            width: '100%'
        });
    }

    $(document).ready(function() {
        $('#btn-item-add').click(function() {
            $('#tb-item > tbody').append(item);
            selectRefresh();
        });

        $('tbody').on('click','#btn-item-delete', function() {
            $(this).parent().parent().remove();
        });
    });
    //--- End repeat item form

    $(document).ready(function() {
        $('#duration').select2({
            tags: true,
            width: '100%'
        });

        selectRefresh();

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
        $('#btnCreateSave').on('click', function () {
            $('#btnCreateSave-loading').toggle();
            $('#btnCreateSave-loading').prop('disabled',true);
            $('#btnCreateSave').toggle();
            $('#btnSave').toggle();
            $('#btnCancel').toggle();
        });

        $('#btnSave').on('click', function () {
            $('#btnSave-loading').toggle();
            $('#btnSave-loading').prop('disabled',true);
            $('#btnCreateSave').toggle();
            $('#btnSave').toggle();
            $('#btnCancel').toggle();
        });
        //end
    });
</script>
@endpush