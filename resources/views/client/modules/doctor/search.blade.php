@extends('master.client.layout.app')

@push('plugin-style')
    <!-- Datetimepicker CSS -->
    <link rel="stylesheet" href="{{ asset('assets/client/css/bootstrap-datetimepicker.min.css') }}">
    
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/client/plugins/select2/css/select2.min.css') }}">
    
    <!-- Fancybox CSS -->
    <link rel="stylesheet" href="{{ asset('assets/client/plugins/fancybox/jquery.fancybox.min.css') }}">

    <style>
        /* CSS disabled tanggal yang sudah lewat */
        .disabled {
            pointer-events: none;
            opacity: 0.6;
        }
    </style>
@endpush

@section('breadcrumb')
    <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-12 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('client.home') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Search Doctor</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">Search Doctor</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- /Breadcrumb -->
@endsection

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 col-lg-4 col-xl-3 theiaStickySidebar">
            
                <!-- Search Filter -->
                <div class="card search-filter">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Search Filter</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('client.doctor.search') }}" method="get">
                            <div class="filter-widget">
                                <h4>Select Date</h4>
                                <input name="date" type="date" id="tanggal" class="form-control" placeholder="Select Date"
                                @if (Request::get('date'))
                                    value="{{ Request::get('date') }}"
                                @endif
                                >
                            </div>

                            {{-- Gender --}}
                            {{-- <div class="filter-widget">
                                <h4>Gender</h4>
                                <div>
                                    <label class="custom_check">
                                        <input type="checkbox" name="gender[]" value="M"
                                        @if(is_array(Request::get('gender')) && in_array('M', Request::get('gender'))) 
                                            checked
                                        @endif
                                        >
                                        <span class="checkmark"></span> Male Doctor
                                    </label>
                                </div>
                                <div>
                                    <label class="custom_check">
                                        <input type="checkbox" name="gender[]" value="F"
                                        @if(is_array(Request::get('gender')) && in_array('F', Request::get('gender'))) 
                                            checked
                                        @endif
                                        >
                                        <span class="checkmark"></span> Female Doctor
                                    </label>
                                </div>
                            </div> --}}
                            {{-- End Gender --}}

                            {{-- Speciality --}}
                            <div class="filter-widget">
                                <h4>Select Specialist</h4>
                                @foreach ($speciality as $item)
                                    <div>
                                        <label class="custom_check">
                                            <input type="checkbox" name="specialist[]" value="{{ $item->name }}"
                                            @if(is_array(Request::get('specialist')) && in_array($item->name, Request::get('specialist'))) 
                                                checked
                                            @endif
                                            >
                                            <span class="checkmark"></span> {{ $item->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            {{-- End Speciality --}}

                            {{-- Hospital / Clinic --}}
                            <div class="filter-widget">
                                <h4>Select Clinic</h4>
                                @foreach ($hospital as $item)
                                    <div>
                                        <label class="custom_check">
                                            <input type="checkbox" name="hospital[]" value="{{ $item->name }}"
                                            @if(is_array(Request::get('hospital')) && in_array($item->name, Request::get('hospital'))) 
                                                checked
                                            @endif
                                            >
                                            <span class="checkmark"></span> {{ $item->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            {{-- End Hospital / Clinic --}}
                            
                            <div class="btn-search">
                                <button type="submit" class="btn btn-block">Search</button>
                            </div>
                        </form>	
                    </div>
                <!-- /Search Filter -->
                </div>
            </div>
            
            <div class="col-md-12 col-lg-8 col-xl-9">

                <!-- Doctor Widget -->
                @if ($doctors->count() != null)
                    @foreach ($doctors as $doctor)
                        <div class="card">
                            <div class="card-body">
                                <div class="doctor-widget">
                                    <div class="doc-info-left">
                                        <div class="doctor-img">
                                            <a href="{{ route('client.doctor.show', Crypt::encryptString($doctor->id)) }}">
                                                <img src="{{ $doctor->takePicture }}" class="img-fluid" alt="User Image">
                                            </a>
                                        </div>
                                        <div class="doc-info-cont">
                                            <h4 class="doc-name"><a href="{{ route('client.doctor.show', Crypt::encryptString($doctor->id)) }}">{{ $doctor->name }}</a></h4>
                                            <p class="doc-speciality">{{ $doctor->specialization }} - {{ $doctor->speciality->name }}</p>
                                            <h5 class="doc-department mb-3"><img src="{{ $doctor->speciality->takePicture }}" class="img-fluid" alt="Speciality">{{ $doctor->speciality->name }}</h5>
                                            <div class="clinic-details mb-1">
                                                <span class="doc-location"><i class="fas fa-map-marker-alt"></i>
                                                @foreach ($doctor->doctorLocation as $index => $location)
                                                   @if ($index == 0)
                                                     {{ $location->hospital->name }}
                                                   @else
                                                    , {{ $location->hospital->name }}
                                                   @endif
                                                @endforeach
                                                </span>
                                            </div>
                                            {{-- <div class="mb-3">
                                                @php
                                                    $item = $doctor->practiceSchedules->first();
                                                @endphp
                                                @if ($item == null)
                                                 <i class="far fa-clock"></i> <span class="text-success"></span>
                                                @elseif (\Carbon\Carbon::now()->format('Y-m-d') == $item->date)
                                                    <i class="far fa-clock"></i> <span class="text-success"> Available now</span>
                                                @else
                                                    <i class="far fa-clock"></i> <span class="text-warning"> Available on {{ \Carbon\Carbon::parse($item->date)->format('D, d M') }}</span> 
                                                @endif
                                            </div>   --}}
                                        </div>
                                    </div>
                                    <div class="doc-info-right">
                                        <div class="clinic-booking">
                                            <a class="view-pro-btn" href="{{ route('client.doctor.show', Crypt::encryptString($doctor->id)) }}">View Profile</a>
                                            <a class="apt-btn" href="{{ route('client.patient.booking', Crypt::encryptString($doctor->id)) }}">Book Appointment</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="card">
                        <div class="card-body">
                            <h4>The doctor was not found based on the keywords you selected</h4>
                            <span class="text-muted">Please Search with another key on <strong>"Search Filter"</strong></span>
                        </div>
                    </div>
                @endif
                <!-- /Doctor Widget -->

                {{-- <div class="load-more text-center">
                    <a class="btn btn-primary btn-sm" href="javascript:void(0);">Load More</a>	
                </div>	 --}}
            </div>
        </div>
    </div>
</div>		
@endsection

@push('script')
    <!-- Sticky Sidebar JS -->
    <script src="{{ asset('assets/client/plugins/theia-sticky-sidebar/ResizeSensor.js') }}"></script>
    <script src="{{ asset('assets/client/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js') }}"></script>
    
    <!-- Select2 JS -->
    <script src="{{ asset('assets/client/plugins/select2/js/select2.min.js') }}"></script>
    
    <!-- Datetimepicker JS -->
    <script src="{{ asset('assets/client/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/client/js/bootstrap-datetimepicker.min.js') }}"></script>
    
    <!-- Fancybox JS -->
    <script src="{{ asset('assets/client/plugins/fancybox/jquery.fancybox.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var inputTanggal = document.getElementById('tanggal');
            var tanggalSekarang = new Date();

            // Mengatur nilai minimum input tanggal
            inputTanggal.setAttribute('min', formatTanggal(tanggalSekarang));

            // Mengubah warna tanggal yang sudah lewat
            var tanggalItems = document.querySelectorAll('input[type="date"]');
            tanggalItems.forEach(function(tanggalItem) {
                var tanggalValue = new Date(tanggalItem.value);
                if (tanggalValue < tanggalSekarang) {
                    tanggalItem.classList.add('disabled');
                }
            });

            // Fungsi untuk mengubah format tanggal
            function formatTanggal(date) {
                var tahun = date.getFullYear();
                var bulan = (date.getMonth() + 1).toString().padStart(2, '0');
                var hari = date.getDate().toString().padStart(2, '0');
                return tahun + '-' + bulan + '-' + hari;
            }
        });
    </script>
@endpush