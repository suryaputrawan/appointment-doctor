<!DOCTYPE html>
<html lang="en">
    
<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">

        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>ADOS - {{ $title }}</title>
		
		<!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/admin/img/favicon.png') }}">
		
		<!-- Bootstrap CSS -->
        <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap.min.css') }}">
		
		<!-- Fontawesome CSS -->
        <link rel="stylesheet" href="{{ asset('assets/admin/css/font-awesome.min.css') }}">
		
		<!-- Feathericon CSS -->
        <link rel="stylesheet" href="{{ asset('assets/admin/css/feathericon.min.css') }}">
		
		@stack('plugin-style')

		<!-- Main CSS -->
        <link rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}">
		
		<!--[if lt IE 9]>
			<script src="assets/js/html5shiv.min.js"></script>
			<script src="assets/js/respond.min.js"></script>
		<![endif]-->
    </head>
    <body>
	
		<!-- Main Wrapper -->
        <div class="main-wrapper">
		
			<!-- Header -->
            @include('master.admin.layout.includes.header')
			<!-- /Header -->
			
			<!-- Sidebar -->
            @include('master.admin.layout.includes.sidebar')
			<!-- /Sidebar -->
			
			<!-- Page Wrapper -->
            <div class="page-wrapper">
			
                <div class="content container-fluid">
					
					<!-- Page Header -->
                    @yield('content')
					<!-- /Page Header -->
				</div>			
			</div>
			<!-- /Page Wrapper -->
		
        </div>
		<!-- /Main Wrapper -->
		
		<!-- jQuery -->
        <script src="{{ asset('assets/admin/js/jquery-3.2.1.min.js') }}"></script>
		
		<!-- Bootstrap Core JS -->
        <script src="{{ asset('assets/admin/js/popper.min.js') }}"></script>
        <script src="{{ asset('assets/admin/js/bootstrap.min.js') }}"></script>
		
		<!-- Slimscroll JS -->
        <script src="{{ asset('assets/admin/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
		
		<script src="{{ asset('assets/admin/plugins/raphael/raphael.min.js') }}"></script>

        @stack('plugin-script')
		
		<!-- Custom JS -->
		<script  src="{{ asset('assets/admin/js/script.js') }}"></script>

        <script>
            //----Delete data
            $(document).ready(function() {
                const swalWithBootstrapButtonsConfirm = Swal.mixin();
                const swalWithBootstrapButtons = Swal.mixin();

                $(document).on('click', '.delete-item', function(e) {
                    e.preventDefault();
                    var id = $(this).data('id');
                    var url = $(this).data('url');
                    var label = $(this).data('label');

                    swalWithBootstrapButtonsConfirm.fire({
                        title: `Yakin ingin menghapus [ ${label} ] ?`,
                        text: "Data yang sudah di hapus tidak bisa dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Hapus data',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return $.ajax({
                                type: "POST",
                                dataType: "JSON",
                                url: url,
                                data: {
                                    "_method": 'DELETE',
                                    "_token": "{{ csrf_token() }}",
                                }
                            }).then((data) => {
                                let message = 'Data has been deleted..!';
                                if (data.message) {
                                    message = data.message;
                                }
                                swalWithBootstrapButtons.fire('Berhasil!', message, 'success');
                                $('#datatable').DataTable().ajax.reload();
                                $('#datatable-education').DataTable().ajax.reload();
                                $('#datatable-location').DataTable().ajax.reload();
                            }, (data) => {
                                let message = '';
                                if (data.responseJSON.message) {
                                    message = data.responseJSON.message;
                                }
                                swalWithBootstrapButtons.fire('Oops!', `Gagal menghapus data, ${message}`, 'error');
                                if (data.status === 404) {
                                    $('#datatable').DataTable().ajax.reload();
                                    $('#datatable-education').DataTable().ajax.reload();
                                }
                            });
                        },
                        allowOutsideClick: () => !swalWithBootstrapButtons.isLoading(),
                        backdrop: true
                    });
                });
            });
            //----End Delete data
        </script>

        @stack('custom-scripts')
		
    </body>

<!-- Mirrored from dreamguys.co.in/demo/doccure/admin/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 30 Nov 2019 04:12:34 GMT -->
</html>