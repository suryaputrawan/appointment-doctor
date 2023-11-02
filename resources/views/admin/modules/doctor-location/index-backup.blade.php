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
                        <div class="table-responsive">
                            <table id="datatable" class="datatable table table-stripped" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th style="width: 100px">ACTION</th>
                                        <th>Doctor Name</th>
                                        <th>Locations</th>
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
    @include('admin.modules.doctor-location.modal.action')
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

    let item = "<tr>"+
                    "<td>"+
                        "<input type='text' class='form-control' name='day[]' value='{{ old('day') }}' placeholder='Example : MON - FRI' style='text-transform:uppercase' autocomplete='off'>"+
                        "<p id='error-day' style='color: red' class='error'></p>"+
                    "</td>"+
                    "<td>"+
                        "<input type='text' class='form-control' name='time[]' value='{{ old('time') }}' placeholder='Example : 10:00 AM - 2:00 PM' style='text-transform:uppercase' autocomplete='off'>"+
                        "<p id='error-time' style='color: red' class='error'></p>"+
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
            ajax: "{{ route('admin.doctor-location.index') }}?type=datatable",
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
                { data: "hospital", name: "hospital", orderable: false, searchable: false },
            ],
        });
        //-----End datatable inizialitation

        //----form environtment
        let ajaxUrl = "{{ route('admin.doctor-location.store') }}";
        let ajaxType = "POST";

        function clearForm() {
            $("#location").val("").trigger('change');
            $("#day").val("");
            $("#time").val("");
            $('#doctor-location-form').find('.error').text("");

            ajaxUrl = "{{ route('admin.doctor-location.store') }}";
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

            $('#modal-add-doctor-location').modal('show');
            $('#title').text('Add Doctor Practice Location');
            $('#title-span').text(nama);
            $('#doctor-id').val(id);
            $("#btn-submit-text").text("Save");

            $("#datatable-location").DataTable({
                ...tableOptions,
                ajax: `${url}?type=datatable`,
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
                    { data: "hospital", name: "hospital", orderable: true  },
                    { data: "day", name: "day", orderable: false, searchable: false  },
                    { data: "time", name: "time", orderable: false, searchable: false  },
                    { data: "action", name: "action", orderable: false, searchable: false, className: "text-center", },
                ],
            });
        });

        $(".btn-cancel").click(function() {
            $('#modal-add-doctor-location').modal('hide');
            clearForm();
            $('#datatable').DataTable().ajax.reload();
        });

        $(".btn-clear").click(function() {
            clearForm();
            $('#title').text('Add Doctor Location');
        });
        //----End Modal

        //------ Submit Data
        $('#doctor-location-form').on('submit', function(e) {
            e.preventDefault();

            var submitButton = $(this).find("button[type='submit']");
            var submitButtonLoading = $(this).find("button[type='submit'] #submit-loading");
            submitButton.prop('disabled',true);
            submitButtonLoading.toggle();

            var formData = new FormData(this);
            formData.append("_method", ajaxType)

            $('#doctor-location-form').find('.error').text("");

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
                    // console.log(response);
                    
                    submitButton.prop('disabled',false);
                    submitButtonLoading.toggle();

                    if (response.status == 200) {
                        clearForm();
                        $('#title').text('Add Doctor Location');
                        $('#datatable-location').DataTable().ajax.reload();

                        Toast.fire({
                            icon: 'success',
                            title: response.message,
                        });
                    } else if (response.status == 400) {
                        $.each(response.errors.location, function(key, error) {
                            $('#error-location').append(error);
                        });

                        $.each(response.errors.day, function(key, error) {
                            $('#error-day').append(error);
                        });
                        $.each(response.errors.time, function(key, error) {
                            $('#error-time').append(error);
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
        $(document).on('click', '#btn-edit-location', function(e) {
            e.preventDefault();

            var id = $(this).data('id');
            var url = $(this).data('url');
            var nama = $(this).data('name');
            var doctorId = $(this).data('doctor-id');

            $('#title').text('Edit Doctor Location');
            $('#title-span').text(nama);
            $('#doctor-id').val(doctorId);

            $('#doctor-location-form').find('.error').text("");

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
                    console.log(response)

                    $("#item-loading").hide(500);
                    if (response.status == 404) {
                        clearForm();

                        Toast.fire({
                            icon: 'warning',
                            title: response.message,
                        });
                    } else {
                        ajaxUrl = "{{ route('admin.doctor-location.index') }}/"+response.data.id;
                        ajaxType = "PUT";

                        $('#location').val(response.data.hospital_id).trigger('change');
                        $('#day').val(response.data.day);
                        $('#time').val(response.data.time);
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