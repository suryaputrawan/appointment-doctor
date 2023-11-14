<!DOCTYPE html> 
<html lang="en">
	
<!-- doccure/login.html  30 Nov 2019 04:12:20 GMT -->
<head>
		<meta charset="utf-8">
		<title>ADOS - Forgot Password</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
		
		<!-- Favicons -->
		<link href="{{ asset('assets/client/img/favicon.png') }}" rel="icon">
		
		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="{{ asset('assets/client/css/bootstrap.min.css') }}">
		
		<!-- Fontawesome CSS -->
		<link rel="stylesheet" href="{{ asset('assets/client/plugins/fontawesome/css/fontawesome.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/client/plugins/fontawesome/css/all.min.css') }}">
		
		<!-- Main CSS -->
		<link rel="stylesheet" href="{{ asset('assets/client/css/style.css') }}">
	
	</head>
	<body class="account-page">

		<!-- Main Wrapper -->
		<div class="main-wrapper">

			<!-- Page Content -->
			<div class="content">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12 offset-md-12">
							<!-- Login Tab Content -->
							<div class="account-content">
								<div class="row align-items-center justify-content-center">
									<div class="col-md-7 col-lg-6 login-left">
										<img src="{{ asset('assets/client/img/login-banner.png') }}" class="img-fluid" alt="login">	
									</div>
                                    <div class="col-md-12 col-lg-6 login-right">
										<div class="login-header">
											<h3>Forgot Password?</h3>
											<p class="small text-muted">Enter your email to get a new password</p>
										</div>
										
										<!-- Forgot Password Form -->
										<form action="{{ route('reset.password') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
											<div class="form-group form-focus">
												<input name="email" class="form-control floating @error('email') is-invalid @enderror" type="email">
												<label class="focus-label">Email</label>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
											</div>
											<div class="text-right">
												<a class="forgot-link" href="{{ route('login') }}">Remember your password?</a>
											</div>
                                            <button class="btn btn-primary btn-block btn-lg login-btn" type="submit" id="btnReset">Reset Password</button>
                                            <button class="btn btn-primary btn-block btn-lg login-btn" type="submit" id="btnReset-loading" style="display: none">
                                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                                <span>Reset Password</span>
                                            </button>
										</form>
										<!-- /Forgot Password Form -->
										
									</div>
								</div>
							</div>
							<!-- /Login Tab Content -->
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

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
		
		<!-- Custom JS -->
		<script src="{{ asset('assets/client/js/script.js') }}"></script>

        <script type="text/javascript">

            $(document).ready(function() {
                //Toast for session success
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
        
                @if (session('error')) {
                    Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Oops...',
                    text: "{{ session('error') }}",
                    showConfirmButton: true,
                    });
                }
                @endif

                //environment button
                $('#btnReset').on('click', function () {
                    $('#btnReset-loading').toggle();
                    $('#btnReset-loading').prop('disabled',true);
                    $('#btnReset').toggle();
                });
            });
        </script>
		
	</body>

</html>