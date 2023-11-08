@extends('master.admin.layout.app', ['title' => 'Assign Permissions'])

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
                    <li class="breadcrumb-item">Roles & Permissions</li>
                    <li class="breadcrumb-item">Assign Permissions</li>
                    <li class="breadcrumb-item active">{{ $breadcrumb }}</li>
                </ul>
            </div>
            <div class="col-sm-5 col">
                <a href="{{ route('admin.assign.index') }}" class="btn btn-secondary float-right mt-2" type="button">
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
                        <form action="{{ route('admin.assign.update', Crypt::encryptString($data->id)) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row form-row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label> Role Name <span class="text-danger">*</span></label>
                                        <select name="role" class="select @error('role') is-invalid @enderror">
                                            <option selected disabled>-- Please Selected --</option>
                                            @foreach ($roles as $item)
                                            <option value="{{ $item->id }}"
                                                {{ old('role', $data->id) == $item->id ? 'selected' : null }}>{{ $item->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('role')
                                            <span class="text-danger" style="margin-top: .25rem; font-size: 80%;">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label> Permissions <span class="text-danger">*</span></label>
                                        <div class="checkbox">
                                            @foreach ($permissions as $item)
                                            <label class="mr-3 mb-3">
                                                <input type="checkbox" class="@error('permissions') is-invalid @enderror" name="permissions[]" 
                                                    value="{{ $item->name }}" {{ $data->permissions()->find($item->id) ? "checked" : "" }} > {{ $item->name }}
                                            </label>
                                            @endforeach
                                        </div>
                                        @error('permissions')
                                            <span class="text-danger" style="margin-top: .25rem; font-size: 80%;">{{ $message }}</span>
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
                                <a href="{{ route('admin.assign.index') }}" class="btn btn-danger">Cancel</a>
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
    });
</script>
@endpush