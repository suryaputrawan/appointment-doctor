<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title"> 
                    <span>Main</span>
                </li>
                <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"> 
                    <a href="{{ route('admin.dashboard') }}"><i class="fe fe-home"></i> <span>Dashboard</span></a>
                </li>
                <li class="{{ request()->routeIs('admin.appointment.index') || 
                    request()->routeIs('admin.appointment.create') || 
                    request()->routeIs('admin.appointment.edit') ? 'active' : '' }}"> 
                    <a href="{{ route('admin.appointment.index') }}"><i class="fe fe-layout"></i> <span>Appointments</span></a>
                </li>

                {{-- Menu Master --}}
                <li class="menu-title"> 
                    <span>Master</span>
                </li>
                <li class="{{ request()->routeIs('admin.speciality.index') ? 'active' : '' }}"> 
                    <a href="{{ route('admin.speciality.index') }}"><i class="fe fe-users"></i> <span>Specialities</span></a>
                </li>
                <li class="{{ request()->routeIs('admin.services.index') ? 'active' : '' }}"> 
                    <a href="{{ route('admin.services.index') }}"><i class="fa fa-heartbeat" aria-hidden="true"></i> <span>Services</span></a>
                </li>
                <li class="submenu">
                    <a href="#"><i class="fe fe-user-plus"></i> <span> Doctors</span> <span class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li>
                            <a class="{{ request()->routeIs('admin.doctor.index') ? 'active' : '' }}" href="{{ route('admin.doctor.index') }}">Doctor List</a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.doctor-education.index') ? 'active' : '' }}" href="{{ route('admin.doctor-education.index') }}">Educations</a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.doctor-location.index') || 
                            request()->routeIs('admin.doctor-location.create') || 
                            request()->routeIs('admin.doctor-location.edit') || 
                            request()->routeIs('admin.doctor-location.show') ? 'active' : '' }}" 
                            href="{{ route('admin.doctor-location.index') }}">Practice Locations</a>
                        </li>
                    </ul>
                </li>
                <li class="{{ request()->routeIs('admin.practice-schedules.index') ? 'active' : '' }}"> 
                    <a href="{{ route('admin.practice-schedules.index') }}"><i class="fe fe-calendar"></i> <span>Doctor Schedules</span></a>
                </li>
                <li class="{{ request()->routeIs('admin.hospitals.index') ? 'active' : '' }}"> 
                    <a href="{{ route('admin.hospitals.index') }}"><i class="fa fa-hospital-o" aria-hidden="true"></i> <span>Hospitals</span></a>
                </li>

                {{-- Role & Permission --}}
                <li class="menu-title"> 
                    <span>Role & Permission</span>
                </li>
                <li class="submenu">
                    <a href="#"><i class="fe fe-gear"></i> <span> Roles & Permissions</span> <span class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li>
                            <a class="{{ request()->routeIs('admin.roles.index') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">Roles</a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.permissions.index') ? 'active' : '' }}" href="{{ route('admin.permissions.index') }}">Permissions</a>
                        </li>
                        <li>
                            <a class="{{ request()->routeIs('admin.assign.index') ? 'active' : '' }}" href="{{ route('admin.assign.index') }}">Assign Permissions</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>