@extends('master.client.layout.app')

@push('plugin-style')
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/client/plugins/select2/css/select2.min.cs') }}s">
@endpush

@section('banner')
<section class="section section-search">
    <div class="container-fluid">
        <div class="banner-wrapper">
            <div class="banner-header text-center">
                <h1>Search Doctor, Make an Appointment</h1>
                {{-- <p>Discover the best doctors, clinic & hospital the city nearest to you.</p> --}}
            </div>
                
            <!-- Search -->
            <div class="search-box">
                <form action="{{ route('client.doctor.search') }}" method="GET">
                    @csrf
                    <div class="form-group search-location">
                        <select name="hospital[]" class="form-control select">
                            <option selected disabled>Select Hospital / Clinic</option>
                            @foreach ($hospital as $data)
                            <option value="{{ $data->name }}">{{ $data->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group search-info">
                        <select name="speciality[]" class="form-control select">
                            <option selected disabled>Select Specialities</option>
                            @foreach ($specialities as $data)
                            <option value="{{ $data->name }}">{{ $data->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary search-btn"><i class="fas fa-search"></i> <span>Search</span></button>
                </form>
            </div>
            <!-- /Search -->
            
        </div>
    </div>
</section>
@endsection

@section('specialities')
<section class="section section-specialities">
    <div class="container-fluid">
        <div class="section-header text-center">
            <h2>Clinic and Specialities</h2>
            {{-- <p class="sub-title">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p> --}}
        </div>
        <div class="row justify-content-center">
            <div class="col-md-9">
                <!-- Slider -->
                <div class="specialities-slider slider">
                    <!-- Slider Item -->
                    @if ($specialities->count() != null)
                        @foreach ($specialities as $speciality)
                            <div class="speicality-item text-center">
                                <div class="speicality-img">
                                    <img src="{{ $speciality->takePicture }}" class="img-fluid" alt="Speciality">
                                    <span><i class="fa fa-circle" aria-hidden="true"></i></span>
                                </div>
                                <p>{{ $speciality->name }}</p>
                            </div>
                        @endforeach
                    @endif
                    <!-- /Slider Item -->  
                </div>
                <!-- /Slider -->
            </div>
        </div>
    </div>
</section>	
@endsection

@section('doctor')
<section class="section section-doctor">
    <div class="container-fluid">

        <div class="section-header text-center">
            <h2>Book Our Doctor Specialist</h2>
            {{-- <p class="sub-title">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p> --}}
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="doctor-slider slider">
                    <!-- Doctor Widget -->
                    @if ($doctors->count() != null)
                        @foreach ($doctors as $doctor)
                            <div class="profile-widget">
                                <div class="doc-img">
                                    <a href="{{ route('client.doctor.show', Crypt::encryptString($doctor->id)) }}">
                                        <img class="img-fluid" alt="User Image" src="{{ $doctor->takePicture }}">
                                    </a>
                                </div>
                                <div class="pro-content">
                                    <h6><a href={{ route('client.doctor.show', Crypt::encryptString($doctor->id)) }}">{{ $doctor->name }}</a></h6>
                                    <p class="speciality">{{ $doctor->specialization }} - {{ $doctor->speciality->name }}</p>
                                    <ul class="available-info">
                                        <div class="row">
                                            <div class="col-1 col-md-1 col-sm-1">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </div>
                                            <div class="col-10 col-md-10 col-sm-10">
                                                @foreach ($doctor->doctorLocation as $index => $location)
                                                    @if ($index == 0)
                                                        {{ $location->hospital->name }}
                                                    @else
                                                        , {{ $location->hospital->name }}
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        <li>
                                            <?php
                                            $item = $doctor->practiceSchedules->first();
                                            ?>
                                            @if ($item == null)
                                                <i class="far fa-clock"></i> <span class="text-success"></span>
                                            @elseif (\Carbon\Carbon::now()->format('Y-m-d') == $item->date)
                                                <i class="far fa-clock"></i> <span class="text-success"> Available now</span>
                                                <i class="fas fa-check-circle verified"></i>
                                            @else
                                                <i class="far fa-clock"></i> <span class="text-warning"> Available on {{ \Carbon\Carbon::parse($item->date)->format('D, d M') }}</span> 
                                            @endif
                                        </li>
                                    </ul>
                                    <div class="row row-sm">
                                        <div class="col-6">
                                            <a href="{{ route('client.doctor.show', Crypt::encryptString($doctor->id)) }}" class="btn view-btn">View Profile</a>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{ route('client.patient.booking', Crypt::encryptString($doctor->id)) }}" class="btn book-btn">Book Now</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    <!-- /Doctor Widget -->
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('services')
<section class="section section-features">
    <div class="container-fluid">
        <div class="section-header text-center">	
            <h2 class="mt-2">Available Services in Our Clinic</h2>
            {{-- <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. </p> --}}
        </div>	
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="features-slider slider">
                    <!-- Slider Item -->
                    @if ($services->count() != null)
                        @foreach ($services as $service)
                            <div class="feature-item text-center">
                                <img src="{{ $service->takePicture }}" class="img-fluid" alt="Service">
                                <p>{{ $service->name }}</p>
                            </div>
                        @endforeach              
                    @endif
                    <!-- /Slider Item -->
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('script')
    <!-- Select2 JS -->
    <script src="{{ asset('assets/client/plugins/select2/js/select2.min.js') }}"></script>
@endpush