@extends('master.admin.layout.app', ['title' => 'Doctors'])

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
            @can('create doctors')
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
                                        <th style="width: 10px">#</th>
                                        @canany(['update doctors', 'delete doctors'])
                                            <th style="width: 100px">Action</th>
                                        @endcanany
                                        <th>Doctor Name</th>
                                        <th>Specialization</th>
                                        <th>Speciality</th>
                                        <th>Clinic</th>
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

    {{-- Modal Action --}}
    @include('admin.modules.doctor.modal.action')
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
    //Preview Image
    function previewImages() {
        var preview = document.querySelector('#preview');
        preview.innerHTML = '';
        var files = document.querySelector('input[type=file]').files;
    
        function readAndPreview(file) {
            // Make sure `file.name` matches our extensions criteria
            if (/\.(jpe?g|png|gif)$/i.test(file.name)) {
                var reader = new FileReader();
                reader.addEventListener('load', function() {
                    var image = new Image();
                    image.height = 150;
                    image.title = file.name;
                    image.src = this.result;
                    preview.appendChild(image);
                }, false);
    
                reader.readAsDataURL(file);
            }
        }
    
        if (files) {
            [].forEach.call(files, readAndPreview);
        }
    };

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
            ajax: "{{ route('admin.doctor.index') }}?type=datatable",
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
                @canany(['update doctors', 'delete doctors'])
                { data: "action", name: "action", orderable: false, searchable: false, className: "text-center", },
                @endcanany
                { data: "doctor", name: "doctor", orderable: true  },
                { data: "specialization", name: "specialization", orderable: false, searchable: false },
                { data: "speciality", name: "speciality", orderable: true },  
                { data: "hospitals", name: "hospitals", orderable: true },  
                { data: "status", name: "status", orderable: false, searchable: false },
            ],
        });
        //-----End datatable inizialitation

        //----form environtment
        let ajaxUrl = "{{ route('admin.doctor.store') }}";
        let ajaxType = "POST";

        function clearForm() {
            $("#doctor-form").find('input').val("");
            $('#doctor-form').find('.error').text("");
            $("#specialities").val("").trigger('change');
            $("#gender").val("").trigger('change');
            $("#about-me").val("");
            $("#status").val("1").trigger('change');
            var preview = document.querySelector('#preview');
            preview.innerHTML = '';
            ajaxUrl = "{{ route('admin.doctor.store') }}";
            ajaxType = "POST";
        }
        //---End Form environment

        //----Modal
        $(document).on('click', '#btn-add', function() {
            $('#modal-add-doctor').modal('show');
            $('#title').text('Add Doctor');
            $("#btn-submit-text").text("Save");
        });

        $(".btn-cancel").click(function() {
            $('#modal-add-doctor').modal('hide');
            clearForm();
        });
        //----End Modal

        //------ Submit Data
        $('#doctor-form').on('submit', function(e) {
            e.preventDefault();

            var submitButton = $(this).find("button[type='submit']");
            var submitButtonLoading = $(this).find("button[type='submit'] #submit-loading");
            submitButton.prop('disabled',true);
            submitButtonLoading.toggle();

            var formData = new FormData(this);
            formData.append("_method", ajaxType)

            $('#doctor-form').find('.error').text("");

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
                        $('#modal-add-doctor').modal('hide');
                        $('#datatable').DataTable().ajax.reload();

                        Toast.fire({
                            icon: 'success',
                            title: response.message,
                        });
                    } else if (response.status == 400) {
                        $.each(response.errors.name, function(key, error) {
                            $('#error-name').append(error);
                        });
                        $.each(response.errors.gender, function(key, error) {
                            $('#error-gender').append(error);
                        });
                        $.each(response.errors.specialization, function(key, error) {
                            $('#error-specialization').append(error);
                        });
                        $.each(response.errors.specialities, function(key, error) {
                            $('#error-specialities').append(error);
                        });
                        $.each(response.errors.email, function(key, error) {
                            $('#error-email').append(error);
                        });
                        $.each(response.errors.about_me, function(key, error) {
                            $('#error-about').append(error);
                        });
                        $.each(response.errors.picture, function(key, error) {
                            $('#error-picture').append(error);
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
            e.preventDefault();
            $('#modal-add-doctor').modal('show');
            $('#title').text('Edit Doctor');

            var id = $(this).data('id');
            var url = $(this).data('url');

            $('#doctor-form').find('.error').text("");

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
                        $('#modal-add-doctor').modal('hide');
                        Toast.fire({
                            icon: 'warning',
                            title: response.message,
                        });
                    } else {
                        ajaxUrl = "{{ route('admin.doctor.index') }}/"+response.data.id;
                        ajaxType = "PUT";

                        $('#name').val(response.data.name);
                        $('#gender').val(response.data.gender).trigger('change');
                        $('#specialization').val(response.data.specialization);
                        $('#specialities').val(response.data.speciality_id).trigger('change');
                        $('#email').val(response.data.email);
                        $('#about-me').val(response.data.about_me);
                        $('#status').val(response.data.isAktif).trigger('change');
                        $('#preview').eq(0).html('<img src="/storage/'+response.data.picture+'"height="150" alt="Preview Gambar">');
                    }
                },
                error: function(response){
                    $('#modal-add-doctor').modal('hide');
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