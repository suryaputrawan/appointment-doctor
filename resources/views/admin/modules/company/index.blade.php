@extends('master.admin.layout.app', ['title' => 'Company'])

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
            <div class="col-sm-5 col">
                <a id="btn-add" class="btn btn-primary float-right mt-2" type="button">
                    Add
                </a>
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
                                        <th>#</th>
                                        <th style="width: 100px">ACTION</th>
                                        <th>Company Name</th>
                                        <th>Address</th>
                                        <th>Email</th>
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
    @include('admin.modules.company.modal.action')
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
    function previewImagesLogo() {
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

    function previewImagesIcon() {
        var previewIcon = document.querySelector('#preview-icon');
        previewIcon.innerHTML = '';
        var files = document.querySelector('#icon').files;
    
        function readAndPreview(file) {
            // Make sure `file.name` matches our extensions criteria
            if (/\.(jpe?g|png|gif)$/i.test(file.name)) {
                var reader = new FileReader();
                reader.addEventListener('load', function() {
                    var image = new Image();
                    image.height = 150;
                    image.title = file.name;
                    image.src = this.result;
                    previewIcon.appendChild(image);
                }, false);
    
                reader.readAsDataURL(file);
            }
        }
    
        if (files) {
            [].forEach.call(files, readAndPreview);
        }
    };
    //---End preview image

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
            ajax: "{{ route('admin.companies.index') }}?type=datatable",
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
                { data: "action", name: "action", orderable: false, searchable: false, className: "text-center", },
                { data: "company_name", name: "company_name", orderable: true  },
                { data: "address", name: "address", orderable: false, searchable: false },
                { data: "email", name: "email", orderable: true, searchable: false },  
            ],
        });
        //-----End datatable inizialitation

        //----form environtment
        let ajaxUrl = "{{ route('admin.companies.store') }}";
        let ajaxType = "POST";

        function clearForm() {
            $("#company-form").find('input').val("");
            $('#company-form').find('.error').text("");

            var preview = document.querySelector('#preview');
            var previewIcon = document.querySelector('#preview-icon');
            preview.innerHTML = '';
            previewIcon.innerHTML = '';

            ajaxUrl = "{{ route('admin.companies.store') }}";
            ajaxType = "POST";
        }
        //---End Form environment

        //----Modal
        $(document).on('click', '#btn-add', function() {
            $('#modal-add-company').modal('show');
            $('#title').text('Add Company');
            $("#btn-submit-text").text("Save");
        });

        $(".btn-cancel").click(function() {
            $('#modal-add-company').modal('hide');
            clearForm();
        });
        //----End Modal

        //------ Submit Data
        $('#company-form').on('submit', function(e) {
            e.preventDefault();

            var submitButton = $(this).find("button[type='submit']");
            var submitButtonLoading = $(this).find("button[type='submit'] #submit-loading");
            submitButton.prop('disabled',true);
            submitButtonLoading.toggle();

            $('.btn-cancel').toggle();

            var formData = new FormData(this);
            formData.append("_method", ajaxType)

            $('#company-form').find('.error').text("");

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
                        $('#modal-add-company').modal('hide');
                        $('#datatable').DataTable().ajax.reload();

                        Toast.fire({
                            icon: 'success',
                            title: response.message,
                        });
                    } else if (response.status == 400) {
                        $('.btn-cancel').toggle();
                        
                        $.each(response.errors.name, function(key, error) {
                            $('#error-name').append(error);
                        });
                        $.each(response.errors.address, function(key, error) {
                            $('#error-address').append(error);
                        });
                        $.each(response.errors.phone, function(key, error) {
                            $('#error-phone').append(error);
                        });
                        $.each(response.errors.whatsapp, function(key, error) {
                            $('#error-whatsapp').append(error);
                        });
                        $.each(response.errors.email, function(key, error) {
                            $('#error-email').append(error);
                        });
                        $.each(response.errors.instagram, function(key, error) {
                            $('#error-instagram').append(error);
                        });
                        $.each(response.errors.facebook, function(key, error) {
                            $('#error-facebook').append(error);
                        });
                        $.each(response.errors.logo, function(key, error) {
                            $('#error-logo').append(error);
                        });
                        $.each(response.errors.icon, function(key, error) {
                            $('#error-icon').append(error);
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
            $('#modal-add-company').modal('show');
            $('#title').text('Edit Company');

            var id = $(this).data('id');
            var url = $(this).data('url');

            $('#company-form').find('.error').text("");

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
                        $('#modal-add-company').modal('hide');
                        Toast.fire({
                            icon: 'warning',
                            title: response.message,
                        });
                    } else {
                        ajaxUrl = "{{ route('admin.companies.index') }}/"+response.data.id;
                        ajaxType = "PUT";

                        $('#name').val(response.data.name);
                        $('#address').val(response.data.address);
                        $('#phone').val(response.data.phone);
                        $('#whatsapp').val(response.data.whatsapp);
                        $('#email').val(response.data.email);
                        $('#instagram').val(response.data.instagram);
                        $('#facebook').val(response.data.facebook);

                        $('#preview').eq(0).html('<img src="/storage/'+response.data.logo+'"height="150" alt="Preview Gambar">');
                        $('#preview-icon').eq(0).html('<img src="/storage/'+response.data.favicon+'"height="150" alt="Preview Gambar">');
                    }
                },
                error: function(response){
                    $('#modal-add-company').modal('hide');
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