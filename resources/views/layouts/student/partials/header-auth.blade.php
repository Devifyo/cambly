{{-- resources/views/partials/header-auth.blade.php --}}
<header class="header header-custom header-fixed inner-header relative">
    <div class="container">
        <nav class="navbar navbar-expand-lg header-nav">
            <div class="navbar-header">
                <a id="mobile_btn" href="javascript:void(0);">
                    <span class="bar-icon"><span></span><span></span><span></span></span>
                </a>
                <a href="{{ route('student.dashboard') }}" class="navbar-brand logo">
                    <span class="logo-text">{{ config('app.name') }}</span>
                </a>
            </div>

            <div class="main-menu-wrapper">
                <div class="menu-header">
                    <a href="{{ route('student.dashboard') }}" class="menu-logo">
                        <span class="logo-text">{{ config('app.name') }}</span>
                    </a>
                    <a id="menu_close" class="menu-close" href="javascript:void(0);">
                        <i class="fas fa-times"></i>
                    </a>
                </div>

                <ul class="main-nav">
                    <li class="{{ request()->routeIs('student.tutors.*')  || request()->routeIs('student.booking.*') ? 'active' : '' }}">
                        <a href="{{ route('student.tutors.search') }}">Book a Lesson</a>
                    </li>

                    <li class="{{ request()->routeIs('student.lessons.*') ? 'active' : '' }}">
                        <a href="{{ route('student.lessons.list') }}">{{ is_impersonating() ? 'Student Lessons' : 'My Lessons' }} </a>
                    </li>
                    @if(!is_impersonating())
                        <li class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('student.dashboard') }}">Dashboard</a>
                        </li>

                        <li class="has-submenu {{ request()->routeIs('student.account.*') || request()->routeIs('student.account.show') ? 'active' : '' }}">
                            <a href="#">Account <i class="fas fa-chevron-down"></i> </a>
                            <ul class="submenu">
                                <li class="{{ request()->routeIs('student.account.show') ? 'active' : '' }}"><a href="{{ route('student.account.show') }}">Account Settings</a></li>
                                <li class="{{ request()->routeIs('student.account.subscription') ? 'active' : '' }}"><a href="{{ route('student.account.subscription') }}">Subscription</a></li>
                                <li class="{{ request()->routeIs('student.account.ticket-history') ? 'active' : '' }}"><a href="{{ route('student.account.ticket-history') }}">Ticket History</a></li>
                                <li class="{{ request()->routeIs('student.account.payment-history') ? 'active' : '' }}"><a href="{{ route('student.account.payment-history') }}">Payment History</a></li>
                                <li class="d-md-none md:hidden"> 
                                    <a href="{{ route('auth.logout') }}" 
                                    onclick="event.preventDefault(); document.getElementById('mobile-logout-form').submit();"
                                    class="text-danger">
                                    Logout
                                    </a>
                                    
                                    <form id="mobile-logout-form" action="{{ route('auth.logout') }}" method="POST" class="d-none" style="display: none;">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
            
            <div class="header-menu">
                @if(!is_impersonating())
                    <ul class="nav header-navbar-rht">
                        <!-- User Menu -->
                        <li class="nav-item dropdown has-arrow logged-item">
                            <a href="#" class="nav-link ps-0" data-bs-toggle="dropdown">
                                <span class="user-img">
                                    <img class="rounded-circle" src="{{ $authUser?->profile_link ?? asset('assets/img/dashboard/profile-06.jpg') }}" width="31" alt="User">
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <div class="user-header">
                                    <div class="avatar avatar-sm">
                                        <img src="{{ $authUser?->profile_link ?? asset('assets/img/dashboard/profile-06.jpg') }}" alt="User Image" class="avatar-img rounded-circle">
                                    </div>
                                    <div class="user-text">
                                        <h6>{{ ucfirst($authUser?->name ?? '') }}</h6>
                                        <p class="text-muted mb-0">{{ ucfirst($authUser?->role_name ?? '') }}</p>
                                    </div>
                                </div>

                                <a class="dropdown-item" href="{{ route('student.dashboard') }}">Dashboard</a>
                                <a class="dropdown-item" href="{{ route('student.account.show') }}">Account Settings</a>

                                <form method="POST" action="{{ route('auth.logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </div>
                        </li>
                    </ul>
                @endif
            </div>
        </nav>
    </div>
</header>
