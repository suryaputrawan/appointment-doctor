@extends('master.admin.layout.app', ['title' => 'Profile User'])

@push('plugin-style')
    
@endpush

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Profile</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                    <li class="breadcrumb-item active">Profile</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
    
    <div class="row">
        <div class="col-md-12">
           <!-- Personal Details -->
           <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.profile.update', Crypt::encryptString($data->id)) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <h5 class="card-title">Personal Details</h5>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3" >
                                        <label for="fullname" class="form-label">Full Name <span style="color: red">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{ old('name') ?? $data->name }}">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3" >
                                        <label for="email" class="form-label">Email <span style="color: red">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" value="{{ old('email') ?? $data->email }}">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                
                            <div class="text-right">
                                <button name="btnSimpan" class="btn btn-primary" type="submit" id="btnSave">{{ $btnSubmit }}</button>
                                <button class="btn btn-primary" type="submit" id="btnSave-loading" style="display: none">
                                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    <span>{{ $btnSubmit }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Personal Details -->
        
        <!-- Change Password Tab -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Change Password</h5>
                <div class="row form-row">
                    <div class="col-md-12 col-lg-12">
                        <form action="{{ route('admin.password.update', Crypt::encryptString($data->id)) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')                   
                            <div class="alert alert-danger p-2" role="alert">
                               <li>Hanya isi jika ingin mengubah kata sandi</li>
                               <li>Password minimal 8 karakter</li>
                               <li>Kata Sandi harus berisi setidaknya satu huruf besar dan satu huruf kecil</li>
                               <li>Sandi harus mengandung setidaknya satu angka</li>
                            </div>
    
                            <div class="col-md-6 border-end-md">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Kata Sandi Saat Ini</label>
                                    <input name="current_password" id="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror"
                                        placeholder="Masukkan kata sandi lama">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> 
    
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Kata Sandi Baru</label>
                                    <input name="password" id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Masukkan kata sandi baru">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Ulangi Kata Sandi</label>
                                    <input name="password_confirmation" id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                        placeholder="Ulangi kata sandi baru">
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
        
                            <div class="text-right">
                                <button class="btn btn-danger" type="submit" id="btnSavePassword">Change Password</button>
                                <button class="btn btn-danger" type="submit" id="btnSave-loading-password" style="display: none">
                                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    <span>Change Password</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Change Password Tab -->
        </div>
    </div>
@endsection

@push('plugin-script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('custom-scripts')
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

        //environment button
        $('#btnSave').on('click', function () {
            $('#btnSave-loading').toggle();
            $('#btnSave-loading').prop('disabled',true);
            $('#btnSave').toggle();
        });

        $('#btnSavePassword').on('click', function () {
            $('#btnSave-loading-password').toggle();
            $('#btnSave-loading-password').prop('disabled',true);
            $('#btnSavePassword').toggle();
        });
    });
</script>
@endpush