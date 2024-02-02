<header class="bg-success text-white">
    <div class="container-fluid position-relative no-side-padding">
        <div class="menu-nav-icon" data-nav-menu="#main-menu"><i class="ion-navicon"></i></div>

        <ul class="main-menu visible-on-click" id="main-menu">
            @guest
                <li><a href="{{ route('login') }}">Login</a></li>
                <li><a href="{{ route('register') }}">Register Dokter</a></li>
            @else
                @php
                    $roles = \App\Role::all();
                    $firstRole = $roles->first();
                    $secondRole = optional($roles->get(1));
                    $firstRoleId = $firstRole ? $firstRole->id : null;
                    $secondRoleId = $secondRole ? $secondRole->id : null;
                @endphp

                @if(Auth::user()->role->id == $firstRoleId)
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                @endif
                @if(Auth::user()->role->id == $secondRoleId)
                    <li><a href="{{ route('author.dashboard') }}">Dashboard</a></li>
                @endif
            @endguest
        </ul><!-- main-menu -->
    </div><!-- container -->
</header>