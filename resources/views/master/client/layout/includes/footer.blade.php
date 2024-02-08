<!-- Footer Top -->
<div class="footer-top">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3 col-md-6">
            
                <!-- Footer Widget -->
                <div class="footer-widget footer-about">
                    <div class="footer-logo">
                        <img src="{{ asset('assets/client/img/logo.png') }}" alt="logo" width="100px" height="35px">
                        <p style="color: white">Appointment Doctor Specialist</p>
                    </div>
                    <div class="footer-about-content">
                        {{-- <div class="social-icon">
                            <ul>
                                <li>
                                    <a href="#" target="_blank"><i class="fab fa-facebook-f"></i> </a>
                                </li>
                                <li>
                                    <a href="#" target="_blank"><i class="fab fa-twitter"></i> </a>
                                </li>
                                <li>
                                    <a href="#" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                                </li>
                                <li>
                                    <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                                </li>
                                <li>
                                    <a href="#" target="_blank"><i class="fab fa-dribbble"></i> </a>
                                </li>
                            </ul>
                        </div> --}}
                    </div>
                </div>
                <!-- /Footer Widget -->
                
            </div>

            <div class="col-lg-9 col-md-6">
                <div class="footer-widget footer-menu">
                    <h2 class="footer-title">Clinic Locations</h2>
                    @php
                        $hospitals = dataHospitals()
                    @endphp
                    <div class="row">
                        @foreach ($hospitals as $item)
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <div class="clinic-content">
                                    <h4 class="clinic-name mb-2" style="color: white">{{ $item->name }}</h4>
                                    <div class="clinic-details mb-0">
                                        <h5 class="clinic-direction mb-2" style="color: whitesmoke">
                                            <i class="fas fa-map-marker-alt mr-2"></i> {{ $item->address }} -
                                            <a href="{{ $item->link_gmap }}" target="_blank" rel="noopener noreferrer">Get Directions</a>
                                        </h5>
                                        <h6 class="clinic-direction mb-2" style="color: whitesmoke">
                                            <i class="fas fa-phone-alt mr-2"></i> {{ $item->phone }}
                                        </h6>
                                        <h6 class="clinic-direction mb-5" style="color: whitesmoke">
                                            <i class="fas fa-envelope mr-2"></i> {{ $item->email }}
                                        </h6>
                                    </div>
                                </div>
                            </div>                        
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Footer Top -->

<!-- Footer Bottom -->
<div class="footer-bottom">
    <div class="container-fluid">
    
        <!-- Copyright -->
        <div class="copyright">
            <div class="row text-right">
                <div class="col-12">
                    <div class="copyright-text text-end">
                        <p class="mb-0"><script>document.write(new Date().getFullYear())</script> &copy; ADOS</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Copyright -->
        
    </div>
</div>
<!-- /Footer Bottom -->