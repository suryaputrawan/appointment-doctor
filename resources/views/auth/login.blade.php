<!DOCTYPE html> 
<html lang="en">
	
<!-- doccure/login.html  30 Nov 2019 04:12:20 GMT -->
<head>
		<meta charset="utf-8">
		<title>ADOS - Login</title>
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
											<h3>Login <span>ADOS</span></h3>
										</div>
										<form action="{{ route('postlogin') }}" method="POST">
                                            @csrf
											<div class="form-group form-focus">
                                                <input name="username" class="form-control floating @error('username') is-invalid @enderror" type="text">
												<label class="focus-label">Username</label>
                                                @error('username')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
											</div>
											<div class="form-group form-focus">
												<input name="password" class="form-control floating @error('username') is-invalid @enderror" type="password" placeholder="Password">
                                                <label class="focus-label">Password</label>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
											</div>
											<div class="text-right">
												<a class="forgot-link" href="#">Forgot Password ?</a>
											</div>
                                            <button class="btn btn-primary btn-block btn-lg login-btn" type="submit" id="btnLogin">Login</button>
                                            <button class="btn btn-primary btn-block btn-lg login-btn" type="submit" id="btnLogin-loading" style="display: none">
                                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                                <span>Login</span>
                                            </button>
										</form>
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
        
                @if (session('success')) {
                    Toast.fire({
                        icon: 'success',
                        title: "{{ session('success') }}",
                    });
                }
                @endif

                @if (session('error')) {
                    Toast.fire({
                        icon: 'error',
                        title: "{{ session('error') }}",
                    });
                }
                @endif

                //environment button
                $('#btnLogin').on('click', function () {
                    $('#btnLogin-loading').toggle();
                    $('#btnLogin-loading').prop('disabled',true);
                    $('#btnLogin').toggle();
                });
            });
        </script>
		
	</body>

</html>