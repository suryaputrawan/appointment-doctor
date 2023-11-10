@extends('master.admin.layout.app', ['title' => 'Doctor Practice Schedules'])

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
                    <li class="breadcrumb-item active">{{ $breadcrumb }}</li>
                </ul>
            </div>
            @can('create doctor schedules')
                <div class="col-sm-5 col">
                    <a href="{{ route('admin.practice-schedules.create') }}" class="btn btn-primary float-right mt-2" type="button">
                        Add
                    </a>
                </div>
            @endcan  
        </div>
        
        <div class="row">
            <div class="col-sm-12 mt-3">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable" class="datatable table table-stripped" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        @canany(['update doctor schedules', 'delete doctor schedules'])
                                            <th style="width: 100px">ACTION</th> 
                                        @endcanany
                                        <th>Date</th>
                                        <th>Doctor Name</th>
                                        <th>Time</th>
                                        <th>Hospital / Clinic</th>
                                        <th>Booking Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>			
        </div>
    </div>

    {{-- Modal Action --}}
    @include('admin.modules.practice-schedule.modal.action')
    {{-- End Modal Action --}}

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

        //datatable initialization
        let dataTable = $("#datatable").DataTable({
            ajax: "{{ route('admin.practice-schedules.index') }}?type=datatable",
            processing: true,
            serverSide : true,
            responsive: true,
            destroy: true,
            columns: [
                {
                    data: "id",
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false, searchable: false,
                    className: "text-center",
                },
                @canany(['update doctor schedules', 'delete doctor schedules'])
                { data: "action", name: "action", orderable: false, searchable: false, className: "text-center", },
                @endcanany
                { data: "date", name: "date", orderable: true  },
                { data: "doctor_name", name: "doctor_name", orderable: false, searchable: false },
                { data: "time", name: "time", orderable: false, searchable: false, className: "text-center" },
                { data: "hospital", name: "hospital", orderable: false, searchable: false, className: "text-center" },  
                { data: "booking_status", name: "booking_status", orderable: true, searchable: false, className: "text-center" },  
            ],
        });
        //-----End datatable inizialitation

        //----form environtment
        let ajaxUrl = "{{ route('admin.practice-schedules.store') }}";
        let ajaxType = "POST";

        function clearForm() {
            $("#practice-schedule-form").find('input').val("");
            $('#practice-schedule-form').find('.error').text("");

            $("#doctor").val("").trigger('change');
            $("#hospital").val("").trigger('change');

            ajaxUrl = "{{ route('admin.practice-schedules.store') }}";
            ajaxType = "POST";
        }
        //---End Form environment

        //----Modal
        $(document).on('click', '#btn-add', function() {
            $('#modal-add-practice-schedule').modal('show');
            $('#title').text('Add Doctor Practice Schedule');
            $("#btn-submit-text").text("Save");
        });

        $(".btn-cancel").click(function() {
            $('#modal-add-practice-schedule').modal('hide');
            clearForm();
        });
        //----End Modal

        //------ Submit Data
        $('#practice-schedule-form').on('submit', function(e) {
            e.preventDefault();

            var submitButton = $(this).find("button[type='submit']");
            var submitButtonLoading = $(this).find("button[type='submit'] #submit-loading");
            submitButton.prop('disabled',true);
            submitButtonLoading.toggle();

            $('.btn-cancel').toggle();

            var formData = new FormData(this);
            formData.append("_method", ajaxType)

            $('#practice-schedule-form').find('.error').text("");

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
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type   : "POST",
                url    : ajaxUrl,
                data   : formData,
                processData: false,
                contentType: false,
                success: function(response) {                   
                    submitButton.prop('disabled',false);
                    submitButtonLoading.toggle();

                    if (response.status == 200) {
                        clearForm();
                        $('.btn-cancel').toggle();
                        $('#modal-add-practice-schedule').modal('hide');
                        $('#datatable').DataTable().ajax.reload();

                        Toast.fire({
                            icon: 'success',
                            title: response.message,
                        });
                    } else if (response.status == 400) {
                        $('.btn-cancel').toggle();

                        $.each(response.errors.hospital, function(key, error) {
                            $('#error-hospital').append(error);
                        });

                        $.each(response.errors.date, function(key, error) {
                            $('#error-date').append(error);
                        });
                        $.each(response.errors.doctor, function(key, error) {
                            $('#error-doctor').append(error);
                        });
                        $.each(response.errors.start_time, function(key, error) {
                            $('#error-start-time').append(error);
                        });
                        $.each(response.errors.end_time, function(key, error) {
                            $('#error-end-time').append(error);
                        });
                    } else if (response.status == 404) {
                        $('.btn-cancel').toggle();

                        $('#error-date').append(response.errorDate);
                        $('#error-start-time').append(response.errorStartTime);
                        $('#error-end-time').append(response.errorEndTime);
                    } else {
                        Toast.fire({
                            icon: 'warning',
                            title: response.message,
                        });
                    }
                },
                error: function(response){
                    submitButton.prop('disabled',false);
                    submitButtonLoading.toggle();

                    $('.btn-cancel').toggle();

                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.message ?? 'Oops,.. Something went wrong!',
                    });
                }
            });
        });
        //------ End Submit Data

        //------ Load data to edit
        $(document).on('click', '#btn-edit', function(e) {
            e.preventDefault();
            $('#modal-add-practice-schedule').modal('show');
            $('#title').text('Edit Doctor Practice Schedule');

            var id = $(this).data('id');
            var url = $(this).data('url');

            $('#practice-schedule-form').find('.error').text("");

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

            $("#btn-submit-text").text("Save Change");

            $.ajax({
                type   : "GET",
                url    : url,
                success: function(response) {
                    if (response.status == 404) {
                        clearForm();
                        $('#modal-add-practice-schedule').modal('hide');
                        Toast.fire({
                            icon: 'warning',
                            title: response.message,
                        });
                    } else {
                        ajaxUrl = "{{ route('admin.practice-schedules.index') }}/"+response.data.id;
                        ajaxType = "PUT";

                        $('#date').val(response.data.date);
                        $('#hospital').val(response.data.hospital_id).trigger('change');
                        $('#doctor').val(response.data.doctor_id).trigger('change');
                        $('#start-time').val(response.data.start_time);
                        $('#end-time').val(response.data.end_time);
                    }
                },
                error: function(response){
                    $('#modal-add-practice-schedule').modal('hide');
                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.message ?? 'Oops,.. Something went wrong!',
                    });
                }
            });
        });
        //------ End Load data to edit

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
    });
</script>
@endpush