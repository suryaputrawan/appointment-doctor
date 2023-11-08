<nav class="navbar navbar-expand-lg header-nav">
    <div class="navbar-header">
        <a id="mobile_btn" href="javascript:void(0);">
            <span class="bar-icon">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </a>
        <a href="{{ route('client.home') }}" class="navbar-brand logo">
            <img src="{{ asset('assets/client/img/logo.png') }}" class="img-fluid" alt="Logo">
        </a>
    </div>
    <div class="main-menu-wrapper">
        <div class="menu-header">
            <a href="{{ route('client.home') }}" class="menu-logo">
                <img src="{{ asset('assets/client/img/logo.png') }}" class="img-fluid" alt="Logo">
            </a>
            {{-- <a id="menu_close" class="menu-close" href="javascript:void(0);">
                <i class="fas fa-times"></i>
            </a> --}}
        </div>
        <ul class="main-nav">
            <li class="{{ request()->routeIs('client.home') ? 'active' : '' }}">
                <a href="{{ route('client.home') }}">Home</a>
            </li>
            <li class="{{ request()->routeIs('client.doctor.search') ? 'active' : '' }}">
                <a href="{{ route('client.doctor.search') }}">Search Doctor</a>
            </li>
        </ul>		 
    </div>		 
    <ul class="nav header-navbar-rht">
        {{-- <li class="nav-item contact-item">
            <div class="header-contact-img">
                <i class="far fa-hospital"></i>							
            </div>
            <div class="header-contact-detail">
                <p class="contact-header">Contact</p>
                <p class="contact-info-header"> +62 361 9348888</p>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link header-login" href="login.html">login / Signup </a>
        </li> --}}
    </ul>
</nav>