<header class="header header-custom header-fixed inner-header relative">
    <div class="container">
        <nav class="navbar navbar-expand-lg header-nav">
            <div class="navbar-header">
                <a id="mobile_btn" href="javascript:void(0);">
                    <span class="bar-icon"><span></span><span></span><span></span></span>
                </a>
                <a href="{{ route('teacher.dashboard') }}" class="navbar-brand logo">
                    <span class="logo-text">{{config('app.name')}}</span>
                </a>
            </div>

            <div class="main-menu-wrapper">
                <div class="menu-header">
                    <a href="{{ route('teacher.dashboard') }}" class="menu-logo">
                        {{-- <img src="{{ asset('assets/img/logo.svg') }}" class="img-fluid" alt="Logo"> --}}
                         <span class="logo-text">{{config('app.name')}}</span>
                    </a>
                    <a id="menu_close" class="menu-close" href="javascript:void(0);">
                        <i class="fas fa-times"></i>
                    </a>
                </div>

                <ul class="main-nav">
                <li class="{{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('teacher.dashboard') }}">Dashboard</a>
                </li>

                    <li class="{{ request()->routeIs('teacher.lessons.*') ? 'active' : '' }}">
                        <a href="{{ route('teacher.lessons.list') }}">Manage Lessons </a>
                    </li>

                    <li class="has-submenu {{ request()->routeIs('teacher.account.*') || request()->routeIs('teacher.account.show') ? 'active' : '' }}">
                        <a href="#">Account <i class="fas fa-chevron-down"></i> </a>
                        <ul class="submenu">
                            <li class="{{ request()->routeIs('teacher.account.show') ? 'active' : '' }}"><a href="{{ route('teacher.account.show') }}">Profile settings</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="header-menu">
                <ul class="nav header-navbar-rht">
                    <!-- User Menu -->
                    <li class="nav-item dropdown has-arrow logged-item">
                        <a href="#" class="nav-link ps-0" data-bs-toggle="dropdown">
                            <span class="user-img">
                                <img class="rounded-circle" src="{{ $authUser->profile_link }}" width="31" alt="User">
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div class="user-header">
                                <div class="avatar avatar-sm">
                                    <img src="{{ $authUser->profile_link }}" alt="User Image" class="avatar-img rounded-circle">
                                </div>
                                <div class="user-text">
                                    <h6>Hendrita Hayes</h6>
                                    <p class="text-muted mb-0">Student</p>
                                </div>
                            </div>
                            <a class="dropdown-item" href="{{ route('teacher.dashboard') }}">Dashboard</a>
                            <a class="dropdown-item" href="{{ route('teacher.account.show') }}">Profile Settings</a>
                            <form method="POST" action="{{route('auth.logout')}}">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>
