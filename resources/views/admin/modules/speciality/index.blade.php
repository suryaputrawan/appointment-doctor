@extends('master.admin.layout.app', ['title' => 'Specialities'])

@push('plugin-style')
    <!-- Datatables CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables/datatables.min.css') }}">
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
            @can('create specialities')
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
                                        <th>Speciality</th>
                                        @canany(['update specialities', 'delete specialities'])
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
    @include('admin.modules.speciality.modal.action')
    {{-- End Modal Action --}}

@endsection

@push('plugin-script')
    <!-- Datatables JS -->
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/datatables.min.js') }}"></script>
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
            ajax: "{{ route('admin.speciality.index') }}?type=datatable",
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
                { data: "speciality", name: "speciality", orderable: true  },
                @canany(['update specialities', 'delete specialities'])
                { data: "action", name: "action", orderable: false, searchable: false, className: "text-center", },
                @endcanany
            ],
        });
        //-----End datatable inizialitation

        //----form environtment
        let ajaxUrl = "{{ route('admin.speciality.store') }}";
        let ajaxType = "POST";

        function clearForm() {
            $("#speciality-form").find('input').val("");
            $('#speciality-form').find('.error').text("");
            var preview = document.querySelector('#preview');
            preview.innerHTML = '';
            ajaxUrl = "{{ route('admin.speciality.store') }}";
            ajaxType = "POST";
        }
        //---End Form environment

        //----Modal
        $(document).on('click', '#btn-add', function(e) {
            $('#modal-add-specialities').modal('show');
            $('#title').text('Add Specialities');
            $("#btn-submit-text").text("Save");
        });

        $(".btn-cancel").click(function() {
            $('#modal-add-specialities').modal('hide');
            clearForm();
        });
        //----End Modal

        //------ Submit Data
        $('#speciality-form').on('submit', function(e) {
            e.preventDefault();

            var submitButton = $(this).find("button[type='submit']");
            var submitButtonLoading = $(this).find("button[type='submit'] #submit-loading");
            submitButton.prop('disabled',true);
            submitButtonLoading.toggle();

            var formData = new FormData(this);
            formData.append("_method", ajaxType)

            $('#speciality-form').find('.error').text("");

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
                        $('#modal-add-specialities').modal('hide');
                        $('#datatable').DataTable().ajax.reload();

                        Toast.fire({
                            icon: 'success',
                            title: response.message,
                        });
                    } else if (response.status == 400) {
                        $.each(response.errors.speciality, function(key, error) {
                            $('#error-speciality').append(error);
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
            $('#modal-add-specialities').modal('show');
            $('#title').text('Edit Specialities');

            var id = $(this).data('id');
            var url = $(this).data('url');

            $('#speciality-form').find('.error').text("");

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
                        $('#modal-add-specialities').modal('hide');
                        Toast.fire({
                            icon: 'warning',
                            title: response.message,
                        });
                    } else {
                        ajaxUrl = "{{ route('admin.speciality.index') }}/"+response.data.id;
                        ajaxType = "PUT";

                        $('#speciality').val(response.data.name);
                        $('#preview').eq(0).html('<img src="/storage/'+response.data.picture+'"height="150" alt="Preview Gambar">');
                    }
                },
                error: function(response){
                    $('#modal-add-specialities').modal('hide');
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