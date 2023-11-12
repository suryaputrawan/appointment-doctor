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
                    <li class="breadcrumb-item active">{{ $breadcrumb }}</li>
                </ul>
            </div>
        </div>
        
        <div class="row">
            <div class="col-sm-12 mt-3">
                <div class="card">
                    <div class="card-body">
                        <div class="table">
                            <table id="datatable" class="datatable table table-stripped" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width: 20px">#</th>
                                        <th style="width: 100px">ACTION</th>
                                        <th>Doctor Name</th>
                                        <th>Educations</th>
                                    </tr>
                                </thead>
                                <tbody class="align-middle">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>			
        </div>
    </div>

    {{-- Modal Action --}}
    @include('admin.modules.doctor-education.modal.action')
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
            ajax: "{{ route('admin.doctor-education.index') }}?type=datatable",
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
                { data: "action", name: "action", orderable: false, searchable: false, className: "text-center", },
                { data: "name", name: "name", orderable: true  },
                { data: "educations", name: "educations", orderable: false, searchable: false },
            ],
        });
        //-----End datatable inizialitation

        //----form environtment
        let ajaxUrl = "{{ route('admin.doctor-education.store') }}";
        let ajaxType = "POST";

        function clearForm() {
            $("#university").val("");
            $("#specialization").val("");
            $("#start-year").val("");
            $("#end-year").val("");
            $('#doctor-education-form').find('.error').text("");

            ajaxUrl = "{{ route('admin.doctor-education.store') }}";
            ajaxType = "POST";

            $("#btn-submit-text").text("Save");
        }
        //---End Form environment

        //----Modal
        $(document).on('click', '#btn-add', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var nama = $(this).data('name');
            var url = $(this).data('url');

            $('#modal-add-doctor-education').modal('show');
            $('#title').text('Add Doctor Education');
            $('#title-span').text(nama);
            $('#doctor-id').val(id);
            $("#btn-submit-text").text("Save");

            $("#datatable-education").DataTable({
                ...tableOptions,
                ajax: `${url}?type=datatable`,
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
                    { data: "university_name", name: "university_name", orderable: true  },
                    { data: "specialization", name: "specialization", orderable: true  },
                    { data: "year", name: "year", orderable: false, searchable: false  },
                    { data: "action", name: "action", orderable: false, searchable: false, className: "text-center", },
                ],
            });
        });

        $(".btn-cancel").click(function() {
            $('#modal-add-doctor-education').modal('hide');
            clearForm();
            $('#datatable').DataTable().ajax.reload();
        });

        $(".btn-clear").click(function() {
            clearForm();
            $('#title').text('Add Doctor Education');
        });
        //----End Modal

        //------ Submit Data
        $('#doctor-education-form').on('submit', function(e) {
            e.preventDefault();

            var submitButton = $(this).find("button[type='submit']");
            var submitButtonLoading = $(this).find("button[type='submit'] #submit-loading");
            submitButton.prop('disabled',true);
            submitButtonLoading.toggle();

            var formData = new FormData(this);
            formData.append("_method", ajaxType)

            $('#doctor-education-form').find('.error').text("");

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
                        $('#title').text('Add Doctor Education');
                        $('#datatable-education').DataTable().ajax.reload();

                        Toast.fire({
                            icon: 'success',
                            title: response.message,
                        });
                    } else if (response.status == 400) {
                        $.each(response.errors.university, function(key, error) {
                            $('#error-university').append(error);
                        });
                        $.each(response.errors.specialization, function(key, error) {
                            $('#error-specialization').append(error);
                        });
                        $.each(response.errors.start_year, function(key, error) {
                            $('#error-start-year').append(error);
                        });
                        $.each(response.errors.end_year, function(key, error) {
                            $('#error-end-year').append(error);
                        });
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

                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.message ?? 'Oops,.. Something went wrong!',
                    });
                }
            });
        });
        //------ End Submit Data

        //------ Load data to edit
        $(document).on('click', '#btn-edit-education', function(e) {
            e.preventDefault();

            var id = $(this).data('id');
            var url = $(this).data('url');
            var nama = $(this).data('name');
            var doctorId = $(this).data('doctor-id');

            $('#title').text('Edit Doctor Education');
            $('#title-span').text(nama);
            $('#doctor-id').val(doctorId);

            $('#doctor-education-form').find('.error').text("");

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

            $("#item-loading").show(500);
            $("#btn-submit-text").text("Save Change");

            $.ajax({
                type   : "GET",
                url    : url,
                success: function(response) {
                    $("#item-loading").hide(500);
                    if (response.status == 404) {
                        clearForm();

                        Toast.fire({
                            icon: 'warning',
                            title: response.message,
                        });
                    } else {
                        ajaxUrl = "{{ route('admin.doctor-education.index') }}/"+response.data.id;
                        ajaxType = "PUT";

                        $('#university').val(response.data.university_name);
                        $('#specialization').val(response.data.specialization);
                        $('#start-year').val(response.data.start_year);
                        $('#end-year').val(response.data.end_year);
                    }
                },
                error: function(response){
                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.message ?? 'Oops,.. Something went wrong!',
                    });
                }
            });
        });
        //------ End Load data to edit
    });
</script>
@endpush