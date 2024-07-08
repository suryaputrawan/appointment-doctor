<!DOCTYPE html> 
<html lang="en">
	
<head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
		<title>Medical Certificate Sign</title>
		
		<!-- Favicons -->
		<link type="image/x-icon" href="{{ asset('assets/client/img/favicon.png') }}" rel="icon">
		
		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="{{ asset('assets/client/css/bootstrap.min.css') }}">
		
		<!-- Fontawesome CSS -->
		<link rel="stylesheet" href="{{ asset('assets/client/plugins/fontawesome/css/fontawesome.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/client/plugins/fontawesome/css/all.min.css') }}">

        @stack('plugin-style')
		
		<!-- Main CSS -->
		<link rel="stylesheet" href="{{ asset('assets/client/css/style.css') }}">
		
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="assets/js/html5shiv.min.js"></script>
			<script src="assets/js/respond.min.js"></script>
		<![endif]-->
	
	</head>
	<body>
        <div class="main-wrapper">

            <!-- Page Content -->
            <div class="content success-page-cont">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-lg-6">
                        
                            <!-- Success Card -->
                            <div class="card success-card">
                                <div class="card-body">
                                    <div class="success-cont">
                                        <img src="{{ $data->hospital->takeLogo }}" alt="Logo" width="200px"><br><br>
                                        {{-- <i class="fas fa-check"></i> --}}
                                        <h3><strong>Medical Certificate</strong></h3>
                                        <h3><strong>Has Been Sign</strong></h3><br>
                                        <p>Nomor : <strong>{{ $data->nomor }}</strong><br>
                                            Patient Name : <strong>{{ $data->patient_name }}</strong>
                                        </p>
                                        <p></p>
                                        <p><strong><h2 style="color: blue">{{ $data->user->name }}</h2></strong><br>
                                    </div>
                                </div>
                            </div>
                            <!-- /Success Card -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Page Content -->
		   
	    </div>
	    <!-- /Main Wrapper -->
	  
		<!-- jQuery -->
		<script src="{{ asset('assets/client/js/jquery.min.js') }}"></script>
		
		<!-- Bootstrap Core JS -->
		<script src="{{ asset('assets/client/js/popper.min.js') }}"></script>
		<script src="{{ asset('assets/client/js/bootstrap.min.js') }}"></script>
		
		<!-- Slick JS -->
		<script src="{{ asset('assets/client/js/slick.js') }}"></script>
		
		<!-- Custom JS -->
		<script src="{{ asset('assets/client/js/script.js') }}"></script>		
	</body>
</html>