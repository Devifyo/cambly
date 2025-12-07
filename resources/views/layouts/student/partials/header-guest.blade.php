{{-- resources/views/partials/header-guest.blade.php --}}
<header class="header header-custom header-fixed inner-header relative">
    <div class="container">
        <nav class="navbar navbar-expand-lg header-nav">
            <div class="navbar-header">
                <a id="mobile_btn" href="javascript:void(0);">
                    <span class="bar-icon"><span></span><span></span><span></span></span>
                </a>
                <a href="{{ url('/') }}" class="navbar-brand logo">
                    <span class="logo-text">{{ config('app.name') }}</span>
                </a>
            </div>

            <div class="main-menu-wrapper">
                <div class="menu-header">
                    <a href="{{ url('/') }}" class="menu-logo">
                        <span class="logo-text">{{ config('app.name') }}</span>
                    </a>
                    <a id="menu_close" class="menu-close" href="javascript:void(0);">
                        <i class="fas fa-times"></i>
                    </a>
                </div>

                <ul class="main-nav">
                    <li class="{{ request()->routeIs('student.tutors.*') ? 'active' : '' }}"><a href="{{ route('student.tutors.search') }}">Book a Lesson</a></li>
                    <li class="{{ request()->routeIs('cms.about') ? 'active' : '' }}"><a href="{{ route('cms.about') }}"> About us</a></li>
                    <li class="{{ request()->routeIs('cms.contact') ? 'active' : '' }}"><a href="{{ route('cms.contact') }}" target="_blank" rel="noopener noreferrer"> Contact us</a></li>

                </ul>
            </div>

            <div class="header-menu">
                <ul class="nav header-navbar-rht">
                    <li class="nav-item">
                        <a href="{{ route('auth.login') }}" class="btn btn-outline-primary me-2">Login</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('auth.register') }}" class="btn btn-primary">Register</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>
