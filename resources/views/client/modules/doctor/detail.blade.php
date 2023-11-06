@extends('master.client.layout.app')

@section('breadcrumb')
    <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-12 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('client.home') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Doctor Profile</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">Doctor Profile</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- /Breadcrumb -->
@endsection

@section('content')
    <div class="content">
        <div class="container">

            <!-- Doctor Widget -->
            <div class="card">
                <div class="card-body">
                    <div class="doctor-widget">
                        <div class="doc-info-left">
                            <div class="doctor-img">
                                <img src="{{ $doctor->takePicture }}" class="img-fluid" alt="User Image">
                            </div>
                            <div class="doc-info-cont">
                                <h4 class="doc-name">{{ $doctor->name }}</h4>
                                <p class="doc-speciality">{{ $doctor->specialization }} - {{ $doctor->speciality->name }}</p>
                                <p class="doc-department"><img src="{{ $doctor->speciality->takePicture }}" class="img-fluid" alt="Speciality">{{ $doctor->speciality->name }}</p>
                                {{-- <div class="clinic-details">
                                    <p class="doc-location"><i class="fas fa-map-marker-alt"></i> Newyork, USA - <a href="javascript:void(0);">Get Directions</a></p>
                                    <ul class="clinic-gallery">
                                        <li>
                                            <a href="assets/img/features/feature-01.jpg" data-fancybox="gallery">
                                                <img src="assets/img/features/feature-01.jpg" alt="Feature">
                                            </a>
                                        </li>
                                        <li>
                                            <a href="assets/img/features/feature-02.jpg" data-fancybox="gallery">
                                                <img  src="assets/img/features/feature-02.jpg" alt="Feature Image">
                                            </a>
                                        </li>
                                        <li>
                                            <a href="assets/img/features/feature-03.jpg" data-fancybox="gallery">
                                                <img src="assets/img/features/feature-03.jpg" alt="Feature">
                                            </a>
                                        </li>
                                        <li>
                                            <a href="assets/img/features/feature-04.jpg" data-fancybox="gallery">
                                                <img src="assets/img/features/feature-04.jpg" alt="Feature">
                                            </a>
                                        </li>
                                    </ul>
                                </div> --}}
                                {{-- <div class="clinic-services">
                                    <span>Dental Fillings</span>
                                    <span>Teeth Whitneing</span>
                                </div> --}}
                            </div>
                        </div>
                        <div class="doc-info-right">
                            {{-- <div class="clini-infos">
                                <ul>
                                    <li><i class="far fa-thumbs-up"></i> 99%</li>
                                    <li><i class="far fa-comment"></i> 35 Feedback</li>
                                    <li><i class="fas fa-map-marker-alt"></i> Newyork, USA</li>
                                    <li><i class="far fa-money-bill-alt"></i> $100 per hour </li>
                                </ul>
                            </div>
                            <div class="doctor-action">
                                <a href="javascript:void(0)" class="btn btn-white fav-btn">
                                    <i class="far fa-bookmark"></i>
                                </a>
                                <a href="chat.html" class="btn btn-white msg-btn">
                                    <i class="far fa-comment-alt"></i>
                                </a>
                                <a href="javascript:void(0)" class="btn btn-white call-btn" data-toggle="modal" data-target="#voice_call">
                                    <i class="fas fa-phone"></i>
                                </a>
                                <a href="javascript:void(0)" class="btn btn-white call-btn" data-toggle="modal" data-target="#video_call">
                                    <i class="fas fa-video"></i>
                                </a>
                            </div> --}}
                            <div class="clinic-booking">
                                <a class="apt-btn" href="{{ route('client.patient.booking', Crypt::encryptString($doctor->id)) }}">Book Appointment</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Doctor Widget -->
            
            <!-- Doctor Details Tab -->
            <div class="card">
                <div class="card-body pt-0">
                
                    <!-- Tab Menu -->
                    <nav class="user-tabs mb-4">
                        <ul class="nav nav-tabs nav-tabs-bottom nav-justified">
                            <li class="nav-item">
                                <a class="nav-link active" href="#doc_overview" data-toggle="tab">Overview</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#doc_locations" data-toggle="tab">Locations</a>
                            </li>
                        </ul>
                    </nav>
                    <!-- /Tab Menu -->
                    
                    <!-- Tab Content -->
                    <div class="tab-content pt-0">
                    
                        <!-- Overview Content -->
                        <div role="tabpanel" id="doc_overview" class="tab-pane fade show active">
                            <div class="row">
                                <div class="col-md-12 col-lg-9">
                                
                                    <!-- About Details -->
                                    <div class="widget about-widget">
                                        <h4 class="widget-title">About Me</h4>
                                        <p>{{ $doctor->about_me }}</p>
                                    </div>
                                    <!-- /About Details -->
                                
                                    <!-- Education Details -->
                                    <div class="widget education-widget">
                                        <h4 class="widget-title">Education</h4>
                                        <div class="experience-box">
                                            <ul class="experience-list">
                                                @if ($doctorEducations->isEmpty())
                                                    <h4>No Data Education</h4>
                                                @else
                                                    @foreach ($doctorEducations as $education)
                                                        <li>
                                                            <div class="experience-user">
                                                                <div class="before-circle"></div>
                                                            </div>
                                                            <div class="experience-content">
                                                                <div class="timeline-content">
                                                                    <a href="#/" class="name">{{ $education->university_name }}</a>
                                                                    <div>{{ $education->specialization }}</div>
                                                                    <span class="time">{{ $education->start_year }} - {{ $education->end_year }}</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- /Education Details -->
                            
                                    <!-- Experience Details -->
                                    {{-- <div class="widget experience-widget">
                                        <h4 class="widget-title">Work & Experience</h4>
                                        <div class="experience-box">
                                            <ul class="experience-list">
                                                <li>
                                                    <div class="experience-user">
                                                        <div class="before-circle"></div>
                                                    </div>
                                                    <div class="experience-content">
                                                        <div class="timeline-content">
                                                            <a href="#/" class="name">Glowing Smiles Family Dental Clinic</a>
                                                            <span class="time">2010 - Present (5 years)</span>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="experience-user">
                                                        <div class="before-circle"></div>
                                                    </div>
                                                    <div class="experience-content">
                                                        <div class="timeline-content">
                                                            <a href="#/" class="name">Comfort Care Dental Clinic</a>
                                                            <span class="time">2007 - 2010 (3 years)</span>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="experience-user">
                                                        <div class="before-circle"></div>
                                                    </div>
                                                    <div class="experience-content">
                                                        <div class="timeline-content">
                                                            <a href="#/" class="name">Dream Smile Dental Practice</a>
                                                            <span class="time">2005 - 2007 (2 years)</span>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div> --}}
                                    <!-- /Experience Details -->
                        
                                    <!-- Awards Details -->
                                    {{-- <div class="widget awards-widget">
                                        <h4 class="widget-title">Awards</h4>
                                        <div class="experience-box">
                                            <ul class="experience-list">
                                                <li>
                                                    <div class="experience-user">
                                                        <div class="before-circle"></div>
                                                    </div>
                                                    <div class="experience-content">
                                                        <div class="timeline-content">
                                                            <p class="exp-year">July 2019</p>
                                                            <h4 class="exp-title">Humanitarian Award</h4>
                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin a ipsum tellus. Interdum et malesuada fames ac ante ipsum primis in faucibus.</p>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="experience-user">
                                                        <div class="before-circle"></div>
                                                    </div>
                                                    <div class="experience-content">
                                                        <div class="timeline-content">
                                                            <p class="exp-year">March 2011</p>
                                                            <h4 class="exp-title">Certificate for International Volunteer Service</h4>
                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin a ipsum tellus. Interdum et malesuada fames ac ante ipsum primis in faucibus.</p>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="experience-user">
                                                        <div class="before-circle"></div>
                                                    </div>
                                                    <div class="experience-content">
                                                        <div class="timeline-content">
                                                            <p class="exp-year">May 2008</p>
                                                            <h4 class="exp-title">The Dental Professional of The Year Award</h4>
                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin a ipsum tellus. Interdum et malesuada fames ac ante ipsum primis in faucibus.</p>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div> --}}
                                    <!-- /Awards Details -->
                                    
                                    <!-- Services List -->
                                    {{-- <div class="service-list">
                                        <h4>Services</h4>
                                        <ul class="clearfix">
                                            <li>Tooth cleaning </li>
                                            <li>Root Canal Therapy</li>
                                            <li>Implants</li>
                                            <li>Composite Bonding</li>
                                            <li>Fissure Sealants</li>
                                            <li>Surgical Extractions</li>
                                        </ul>
                                    </div> --}}
                                    <!-- /Services List -->
                                    
                                    <!-- Specializations List -->
                                    {{-- <div class="service-list">
                                        <h4>Specializations</h4>
                                        <ul class="clearfix">
                                            <li>Children Care</li>
                                            <li>Dental Care</li>	
                                            <li>Oral and Maxillofacial Surgery </li>	
                                            <li>Orthodontist</li>	
                                            <li>Periodontist</li>	
                                            <li>Prosthodontics</li>	
                                        </ul>
                                    </div> --}}
                                    <!-- /Specializations List -->

                                </div>
                            </div>
                        </div>
                        <!-- /Overview Content -->
                        
                        <!-- Locations Content -->
                        <div role="tabpanel" id="doc_locations" class="tab-pane fade">
                            <!-- Location List -->
                            @if ($doctorLocations->isEmpty())
                                <h5>No Data Location</h5>
                            @else
                                @foreach ($doctorLocations as $doctorLocation)
                                    <div class="location-list">
                                        <div class="row">
                                            <!-- Clinic Content -->
                                            <div class="col-md-6">
                                                <div class="clinic-content">
                                                    <h4 class="clinic-name mb-2"><a href="#">{{ $doctorLocation->hospital->name }}</a></h4>
                                                    <div class="clinic-details mb-0">
                                                        <h5 class="clinic-direction mb-2"> <i class="fas fa-map-marker-alt mr-2"></i> {{ $doctorLocation->hospital->address }} <br></h5>
                                                        <h6 class="clinic-direction mb-2"> <i class="fas fa-phone-alt mr-2"></i> {{ $doctorLocation->hospital->phone }} <br></h6>
                                                        <h6 class="clinic-direction mb-2"> <i class="fas fa-envelope mr-2"></i> {{ $doctorLocation->hospital->email }} <br></h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /Clinic Content -->
                                            
                                            <!-- Clinic Timing -->
                                            <div class="col-md-4">
                                                @foreach ($doctorLocation->doctorLocationDay as $location)
                                                <div class="clinic-timing">
                                                    <div>
                                                        <p class="timings-days">
                                                            <span> {{ $location->day }} </span>
                                                        </p>
                                                        <p class="timings-times">
                                                            <span>
                                                                {{ 
                                                                \Carbon\Carbon::parse($location->start_time)->format('H:i') 
                                                                .' - '. 
                                                                \Carbon\Carbon::parse($location->end_time)->format('H:i') . ' WITA'
                                                                }}
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            <!-- /Clinic Timing -->
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                            <!-- /Location List -->
                        </div>
                        <!-- /Locations Content -->                        
                    </div>
                </div>
            </div>
            <!-- /Doctor Details Tab -->

        </div>
    </div>
@endsection

@push('script')
    <!-- Fancybox JS -->
    <script src="{{ asset('assets/client/plugins/fancybox/jquery.fancybox.min.js') }}"></script>
@endpush