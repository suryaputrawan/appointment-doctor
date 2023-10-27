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
		
		<!-- Main CSS -->
		<link rel="stylesheet" href="{{ asset('assets/client/css/style.css') }}">
		
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="assets/js/html5shiv.min.js"></script>
			<script src="assets/js/respond.min.js"></script>
		<![endif]-->
	
	</head>
	<body>

		<!-- Main Wrapper -->
		{{-- <div class="main-wrapper">
		
			<!-- Header -->
			@include('master.client.layout.includes.header')
			<!-- /Header -->
			
			<!-- Home Banner -->
			@include('master.client.layout.includes.banner')
			<!-- /Home Banner -->
			  
			<!-- Clinic and Specialities -->
			<section class="section section-specialities">
				<div class="container-fluid">
					@yield('specialities')
				</div>   
			</section>	 
			<!-- Clinic and Specialities -->
		  
			<!-- Popular Section -->
			<section class="section section-doctor">
				<div class="container-fluid">
				   @yield('doctor')
				</div>
			</section>
			<!-- /Popular Section -->
		   
		    <!-- Availabe Features -->
		    <section class="section section-features">
				<div class="container-fluid">
				    <div class="row">
						<div class="col-md-5 features-img">
							<img src="{{ asset('assets/client/img/features/feature.png') }}" class="img-fluid" alt="Feature">
						</div>
						<div class="col-md-7">
							<div class="section-header">	
								<h2 class="mt-2">Availabe Features in Our Clinic</h2>
								<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. </p>
							</div>	
							<div class="features-slider slider">
								<!-- Slider Item -->
								<div class="feature-item text-center">
									<img src="{{ asset('assets/client/img/features/feature-01.jpg') }}" class="img-fluid" alt="Feature">
									<p>Patient Ward</p>
								</div>
								<!-- /Slider Item -->
								
								<!-- Slider Item -->
								<div class="feature-item text-center">
									<img src="{{ asset('assets/client/img/features/feature-02.jpg') }}" class="img-fluid" alt="Feature">
									<p>Test Room</p>
								</div>
								<!-- /Slider Item -->
								
								<!-- Slider Item -->
								<div class="feature-item text-center">
									<img src="{{ asset('assets/client/img/features/feature-03.jpg') }}" class="img-fluid" alt="Feature">
									<p>ICU</p>
								</div>
								<!-- /Slider Item -->
								
								<!-- Slider Item -->
								<div class="feature-item text-center">
									<img src="{{ asset('assets/client/img/features/feature-04.jpg') }}" class="img-fluid" alt="Feature">
									<p>Laboratory</p>
								</div>
								<!-- /Slider Item -->
								
								<!-- Slider Item -->
								<div class="feature-item text-center">
									<img src="{{ asset('assets/client/img/features/feature-05.jpg') }}" class="img-fluid" alt="Feature">
									<p>Operation</p>
								</div>
								<!-- /Slider Item -->
								
								<!-- Slider Item -->
								<div class="feature-item text-center">
									<img src="{{ asset('assets/client/img/features/feature-06.jpg') }}" class="img-fluid" alt="Feature">
									<p>Medical</p>
								</div>
								<!-- /Slider Item -->
                            </div>
						</div>
				    </div>
				</div>
			</section>		
			<!-- Availabe Features -->
			
			<!-- Footer -->
            <footer class="footer">
                @include('master.client.layout.includes.footer')
            </footer>
			<!-- /Footer -->
		   
	    </div> --}}

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
		
	</body>
</html>