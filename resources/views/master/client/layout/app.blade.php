<!DOCTYPE html> 
<html lang="en">
	
<head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
		<title>Appointment</title>
		
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
		
			<!-- Header -->
			<header class="header">
				@include('master.client.layout.includes.header')
			</header>
			<!-- /Header -->

            <!-- Breadcrumb-->
            @yield('breadcrumb')
            <!-- /Breadcrumb -->

            <!-- Page Content -->
            @yield('content')
            <!-- /Page Content -->
			
			<!-- Home Banner -->
            @yield('banner')
			<!-- /Home Banner -->
			  
			<!-- Clinic and Specialities -->
            @yield('specialities')
			 
			<!-- Clinic and Specialities -->
		  
			<!-- Popular Section -->
            @yield('doctor')
			<!-- /Popular Section -->
		   
		   <!-- Availabe Features -->
           @yield('services')		
			<!-- Availabe Features -->
			
			<!-- Footer -->
			<footer class="footer">
				@include('master.client.layout.includes.footer')
			</footer>
			<!-- /Footer -->
		   
	    </div>
	    <!-- /Main Wrapper -->
	  
		<!-- jQuery -->
		<script src="{{ asset('assets/client/js/jquery.min.js') }}"></script>
		
		<!-- Bootstrap Core JS -->
		<script src="{{ asset('assets/client/js/popper.min.js') }}"></script>
		<script src="{{ asset('assets/client/js/bootstrap.min.js') }}"></script>
		
		<!-- Slick JS -->
		<script src="{{ asset('assets/client/js/slick.js') }}"></script>

        @stack('script')
		
		<!-- Custom JS -->
		<script src="{{ asset('assets/client/js/script.js') }}"></script>

        @stack('custom-script')
		
	</body>
</html>