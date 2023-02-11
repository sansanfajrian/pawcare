<aside id="leftsidebar" class="sidebar">
    <!-- User Info -->
    <div >
        <div class="image">
            <img src="{{ url('uploads/profile/'.Auth::user()->image) }}" width="48" height="48" alt="User" />
        </div>
       
        <div class="name btn btn-danger" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color: white; opacity: 75%;">{{ Auth::user()->name }}</div>
        <div class="info-container">
            
            <!-- <div class="email">{{ Auth::user()->email }}</div> -->
            <div class="btn-group user-helper-dropdown">
                <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                <ul class="dropdown-menu pull-right">

                    <li>
                        <a href="{{ Auth::user()->role->id == 1 ? route('admin.settings') : route('author.settings')}}"><i class="material-icons">settings</i>Settings</a>
                    </li>

                    <li role="seperator" class="divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            <i class="material-icons">input</i>Sign Out
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- #User Info -->
    <!-- Menu -->
    <div class="menu">
        <ul class="list">
            <li class="header">MAIN NAVIGATION</li>

            @if(Request::is('admin*'))
                <li class="{{ Request::is('admin/dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="material-icons">dashboard</i>
                        <span>Dashboard</span>
                    </a>
                <li class="{{ Request::is('admin/approvals') ? 'active' : '' }}">
                    <a href="{{ route('admin.approvals.index') }}">
                        <i class="material-icons">approval</i>
                        <span>Doctor Approvals</span>
                    </a>
                </li>
                <li class="{{ Request::is('admin/users') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.index') }}">
                        <i class="material-icons">person</i>
                        <span>Users</span>
                    </a>
                </li>
                <li class="{{ Request::is('admin/doctors') ? 'active' : '' }}">
                    <a href="{{ route('admin.doctors.index') }}">
                        <i class="material-icons">medication</i>
                        <span>Doctors</span>
                    </a>
                </li>
                <li class="{{ Request::is('admin/consultations') ? 'active' : '' }}">
                    <a href="{{ route('admin.consultations.index') }}">
                        <i class="material-icons">list</i>
                        <span>Consultations</span>
                    </a>
                </li>
                <li class="{{ Request::is('admin/banner') ? 'active' : '' }}">
                    <a href="{{ route('admin.banner.index') }}">
                        <i class="material-icons">image</i>
                        <span>Banner</span>
                    </a>
                </li>
                <li class="{{ Request::is('admin/payment') ? 'active' : '' }}">
                    <a href="{{ route('admin.payment.index') }}">
                        <i class="material-icons">payments</i>
                        <span>Payment</span>
                    </a>
                </li>

                <li class="header">System</li>

                <li class="{{ Request::is('admin/settings') ? 'active' : '' }}">
                    <a href="{{ route('admin.settings') }}">
                        <i class="material-icons">settings</i>
                        <span>Settings</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                        <i class="material-icons">input</i>
                        <span>Logout</span>
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            @endif
            @if(Request::is('author*'))
                <li class="{{ Request::is('author/dashboard') ? 'active' : '' }}">
                    <a href="{{ route('author.dashboard') }}">
                        <i class="material-icons">dashboard</i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="{{ Request::is('author/consultations') ? 'active' : '' }}">
                    <a href="{{ route('author.consultations.index') }}">
                        <i class="material-icons">list</i>
                        <span>Consultations</span>
                    </a>
                </li>
                <li class="{{ Request::is('author/payment') ? 'active' : '' }}">
                    <a href="{{ route('author.payment.index') }}">
                        <i class="material-icons">payments</i>
                        <span>Payment</span>
                    </a>
                </li>
                <li class="{{ Request::is('author/reviews') ? 'active' : '' }}">
                    <a href="{{ route('author.reviews.index') }}">
                        <i class="material-icons">star</i>
                        <span>Reviews</span>
                    </a>
                </li>
                <li class="header">System</li>
                <li class="{{ Request::is('author/settings') ? 'active' : '' }}">
                    <a href="{{ route('author.settings') }}">
                        <i class="material-icons">settings</i>
                        <span>Settings</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                        <i class="material-icons">input</i>
                        <span>Logout</span>
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            @endif

        </ul>
    </div>
    <!-- #Menu -->
    <!-- Footer -->
    <div class="legal">
        <div class="copyright">
            &copy; 2023 - {{ date("Y") }} All rights reserved. <br>
            <strong> Developed &amp; <i class="far fa-heart"></i> by </strong>
                        <a href="https://www.youtube.com/channel/UCwbVAlvrsD2Tpx93bleNbOA" target="_blank">PawCare</a>.
        </div>
        <div class="version">
            <b>Version: </b> 1.0.5
        </div>
    </div>
    <!-- #Footer -->
</aside>