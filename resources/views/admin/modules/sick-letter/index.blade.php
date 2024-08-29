@extends('master.admin.layout.app', ['title' => 'Letter Types'])

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
            @can('create sick letter')
                <div class="col-sm-5 col">
                    <a id="btn-add" class="btn btn-primary float-right mt-2" type="button">
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
                                        <th style="width: 20px">#</th>
                                        <th>Nomor</th>
                                        <th>Name</th>
                                        <th>Created By</th>
                                        @canany(['update sick letter', 'print sick letter', 'delete sick letter'])
                                            <th style="width: 100px">ACTION</th> 
                                        @endcanany
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
    @include('admin.modules.sick-letter.modal.action')
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
            ajax: "{{ route('admin.sick-letters.index') }}?type=datatable",
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
                { data: "nomor", name: "nomor", orderable: true, searchable:true },
                { data: "patient_name", name: "patient_name", orderable: true, searchable:true, },
                @canany(['update sick letter', 'print sick letter', 'delete sick letter'])
                { data: "user", name: "user", orderable: true, searchable:true, },
                { data: "action", name: "action", orderable: false, searchable: false, className: "text-center", },
                @endcanany
            ],
        });
        //-----End datatable inizialitation

        //----form environtment
        let ajaxUrl = "{{ route('admin.sick-letters.store') }}";
        let ajaxType = "POST";

        function clearForm() {
            $("#sick-letter-form").find('input').val("");
            $('#sick-letter-form').find('.error').text("");
            $("#gender").val("").trigger('change');

            ajaxUrl = "{{ route('admin.sick-letters.store') }}";
            ajaxType = "POST";
        }
        //---End Form environment

        //----Modal
        $(document).on('click', '#btn-add', function(e) {
            $('#modal-add-sick-letter').modal('show');
            $('#title').text('Create Sick Letter');
            $("#btn-submit-text").text("Save");
        });

        $(".btn-cancel").click(function() {
            $('#modal-add-sick-letter').modal('hide');
            clearForm();
        });
        //----End Modal

        //------ Submit Data
        $('#sick-letter-form').on('submit', function(e) {
            e.preventDefault();

            var submitButton = $(this).find("button[type='submit']");
            var submitButtonLoading = $(this).find("button[type='submit'] #submit-loading");
            submitButton.prop('disabled',true);
            submitButtonLoading.toggle();

            var formData = new FormData(this);
            formData.append("_method", ajaxType)

            $('#sick-letter-form').find('.error').text("");

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
                        $('#modal-add-sick-letter').modal('hide');
                        $('#datatable').DataTable().ajax.reload();

                        Toast.fire({
                            icon: 'success',
                            title: response.message,
                        });
                    } else if (response.status == 400) {
                        $.each(response.errors.name, function(key, error) {
                            $('#error-name').append(error);
                        });
                        $.each(response.errors.patient_email, function(key, error) {
                            $('#error-patient_email').append(error);
                        });
                        $.each(response.errors.age, function(key, error) {
                            $('#error-age').append(error);
                        });
                        $.each(response.errors.gender, function(key, error) {
                            $('#error-gender').append(error);
                        });
                        $.each(response.errors.profession, function(key, error) {
                            $('#error-profession').append(error);
                        });
                        $.each(response.errors.address, function(key, error) {
                            $('#error-address').append(error);
                        });
                        $.each(response.errors.start_date, function(key, error) {
                            $('#error-start_date').append(error);
                        });
                        $.each(response.errors.end_date, function(key, error) {
                            $('#error-end_date').append(error);
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
        $(document).on('click', '#btn-edit', function(e) {
            $('#modal-add-sick-letter').modal('show');
            $('#title').text('Edit Sick Letter');

            var id = $(this).data('id');
            var url = $(this).data('url');

            $('#sick-letter-form').find('.error').text("");

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
                        $('#modal-add-sick-letter').modal('hide');
                        Toast.fire({
                            icon: 'warning',
                            title: response.message,
                        });
                    } else {
                        ajaxUrl = "{{ route('admin.sick-letters.index') }}/"+response.data.id;
                        ajaxType = "PUT";

                        $('#name').val(response.data.patient_name);
                        $('#patient_email').val(response.data.patient_email);
                        $('#age').val(response.data.age);
                        $('#gender').val(response.data.gender).trigger('change');
                        $('#profession').val(response.data.profession);
                        $('#address').val(response.data.address);
                        $('#start_date').val(response.data.start_date);
                        $('#end_date').val(response.data.end_date);
                        $('#diagnosis').val(response.data.diagnosis);
                    }
                },
                error: function(response){
                    $('#modal-add-sick-letter').modal('hide');
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