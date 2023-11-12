@extends('master.admin.layout.app', ['title' => 'Appointments'])

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
            @can('create appointment')
                <div class="col-sm-5 col">
                    <a href="{{ route('admin.appointment.create') }}" class="btn btn-primary float-right mt-2" type="button">
                        Add
                    </a>
                </div>  
            @endcan
        </div>
        
        <div class="row">
            <div class="col-sm-12 mt-3">
                <div class="card">
                    <div class="card-body">
                        <div class="table">
                            <table id="datatable" class="datatable table table-stripped" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        @canany(['update appointment', 'view appointment', 'arrived appointment','cancel appointment'])
                                            <th style="width: 100px">Action</th> 
                                        @endcanany
                                        <th>Date & Time</th>
                                        <th>Booking Number</th>
                                        <th>Doctor</th>
                                        <th>Patient Name</th>
                                        <th>Status</th>
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

    {{-- Modal --}}
    @include('admin.modules.appointment.modal.show')
    {{-- End Modal --}}

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
        var tableOptions = {
            "aLengthMenu": [
                [10, 30, 50, -1],
                [10, 30, 50, "All"]
            ],
            "iDisplayLength": 10,
            "language": {
                search: ""
            }
        };

        let dataTable = $("#datatable").DataTable({
            ...tableOptions,
            ajax: "{{ route('admin.appointment.index') }}?type=datatable",
            processing: true,
            serverSide : true,
            responsive: false,
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
                @canany(['update appointment', 'view appointment', 'arrived appointment','cancel appointment'])
                    { data: "action", name: "action", orderable: false, searchable: false, className: "text-center", },
                @endcan
                { data: "date", name: "date", orderable: true, className: "text-center",  },
                { data: "booking_number", name: "booking_number", orderable: true, searchable: false, className: "text-center", },
                { data: "doctor", name: "doctor", orderable: true },  
                { data: "patient_name", name: "patient_name", orderable: false, searchable: true },
                { data: "status", name: "status", orderable: false, searchable: false },
            ],
        });
        //-----End datatable inizialitation


        //Proses status appointment
        const swalWithBootstrapButtonsConfirm = Swal.mixin();
        const swalWithBootstrapButtons = Swal.mixin();

        //---Status Arrived
        $(document).on('click', '.arrived-item', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var url = $(this).data('url');
            var label = $(this).data('label');

            swalWithBootstrapButtonsConfirm.fire({
                title: `Arrived patient [# ${label} ] ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Arrived',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return $.ajax({
                        type: "POST",
                        dataType: "JSON",
                        url: url,
                        data: {
                            "_method": 'PUT',
                            "_token": "{{ csrf_token() }}",
                        }
                    }).then((data) => {
                        let message = 'Patient has been change to status arrived';
                        if (data.message) {
                            message = data.message;
                        }
                        swalWithBootstrapButtons.fire('Success!', message, 'success');
                        $('#datatable').DataTable().ajax.reload();
                    }, (data) => {
                        let message = '';
                        if (data.responseJSON.message) {
                            message = data.responseJSON.message;
                        }
                        swalWithBootstrapButtons.fire('Oops!', `Arrived not work, ${message}`, 'error');
                        if (data.status === 404) {
                            $('#datatable').DataTable().ajax.reload();
                        }
                    });
                },
                allowOutsideClick: () => !swalWithBootstrapButtons.isLoading(),
                backdrop: true
            });
        });

        //---Status Cancel
        $(document).on('click', '.cancel-item', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var url = $(this).data('url');
            var label = $(this).data('label');

            swalWithBootstrapButtonsConfirm.fire({
                title: `Cancel patient [# ${label} ] ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Proses',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return $.ajax({
                        type: "POST",
                        dataType: "JSON",
                        url: url,
                        data: {
                            "_method": 'PUT',
                            "_token": "{{ csrf_token() }}",
                        }
                    }).then((data) => {
                        let message = 'Patient has been change to status cancel';
                        if (data.message) {
                            message = data.message;
                        }
                        swalWithBootstrapButtons.fire('Success!', message, 'success');
                        $('#datatable').DataTable().ajax.reload();
                    }, (data) => {
                        let message = '';
                        if (data.responseJSON.message) {
                            message = data.responseJSON.message;
                        }
                        swalWithBootstrapButtons.fire('Oops!', `Cancel status not work, ${message}`, 'error');
                        if (data.status === 404) {
                            $('#datatable').DataTable().ajax.reload();
                        }
                    });
                },
                allowOutsideClick: () => !swalWithBootstrapButtons.isLoading(),
                backdrop: true
            });
        });

        //------ Load data to view
        $(document).on('click', '#btn-view', function(e) {
            e.preventDefault();
            
            var id = $(this).data('id');
            var url = $(this).data('url');
            var nama = $(this).data('name');

            $('#modal-view-appointment').modal('show');
            $('#title').text('View Appointment');
            $('#title-span').text(nama);

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
                type   : "GET",
                url    : url,
                success: function(response) {
                    if (response.status == 404) {
                        $('#modal-view-appointment').modal('hide');
                        Toast.fire({
                            icon: 'warning',
                            title: response.message,
                        });
                    } else {
                        var arrbulan = ["","Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
                        let tempDate = new Date(response.data.date);
                        let dateBooking = [ tempDate.getDate(), arrbulan[parseInt(tempDate.getMonth() + 1)], tempDate.getFullYear()].join(' ');

                        let tempDob = new Date(response.data.patient_dob);
                        let dateDob = [ tempDob.getDate(), arrbulan[parseInt(tempDob.getMonth() + 1)], tempDob.getFullYear()].join(' ');

                        $('#booking-view').text(response.data.booking_number);
                        $('#date-view').text(dateBooking);
                        $('#time-view').text(response.data.start_time + ' - ' + response.data.end_time + ' Wita' );
                        $('#hospital-view').text(response.data.hospital.name);
                        $('#doctor-view').text(response.data.doctor.name);
                        $('#name-view').text(response.data.patient_name);
                        $('#dob-view').text(dateDob);
                        if (response.data.patient_sex == "M") {
                            $('#sex-view').text("Male");
                        } else {
                            $('#sex-view').text("Female");
                        }
                        $('#address-view').text(response.data.patient_address);
                        $('#email-view').text(response.data.patient_email);
                        $('#phone-view').text(response.data.patient_telp);
                        $('#status-view').text(response.data.status);
                    }
                },
                error: function(response){
                    $('#modal-view-appointment').modal('hide');
                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.message ?? 'Oops,.. Something went wrong!',
                    });
                }
            });
        });
        //------ End Load data to view

        //---Button cancel click on modal
        $(".btn-cancel").click(function() {
            $('#modal-view-appointment').modal('hide');
        });
        //---End

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