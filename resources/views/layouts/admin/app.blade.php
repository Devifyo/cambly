<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Dynamic title, default: "Admin" --}}
    <title>@yield('title', 'Admin') - {{ config('app.name') }}</title>

    <meta name="description" content="Mahogo - Admin Dashboard">
    <meta name="keywords" content="admin, dashboard, management, Mahogo">
    <meta name="author" content="Mahogo Admin">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/feathericon.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/plugins/morris/morris.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/custom.css') }}">
    
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    
    <style>
        .logo-text {
            background: linear-gradient(90deg, #0E82FD 0%, #06AED4 70%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
            font-size: 24px;
            display: inline-block;
        }
        
        .sidebar ul li a i[class*="fa"] {
            font-size: 20px !important;
            vertical-align: middle;
            width: 20px;
            line-height: 24px;
        }
        
        .user-img img {
            height: 50px;
            width: 50px;
            object-fit: cover;
        }
        
        .table-user-img {
            height: 52px;
            width: 52px;
            object-fit: cover;
        }
        </style>

{{-- Page-specific CSS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
@stack('css')
    @livewireStyles
</head>

<body>
<div class="main-wrapper">

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            <a href="{{ role_route('admin.dashboard') }}" class="logo">
                <span class="logo-text">{{ config('app.name') }}</span>
            </a>

            <a href="{{ role_route('admin.dashboard') }}" class="logo logo-small">
                <span class="logo-text">{{ config('app.name') }}</span>
            </a>
        </div>

        <a href="javascript:void(0);" id="toggle_btn">
            <i class="fe fe-text-align-left"></i>
        </a>

        <a class="mobile_btn" id="mobile_btn">
            <i class="fa fa-bars"></i>
        </a>

        <ul class="nav user-menu">
            {{-- User Dropdown --}}
            <li class="nav-item dropdown has-arrow">
                <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                    <span class="user-img">
                        <img class="rounded-circle" src="{{ $authUser->profile_link }}" width="31" alt="User">
                    </span>
                </a>

                <div class="dropdown-menu">
                    <div class="user-header">
                        <div class="avatar avatar-sm">
                            <img src="{{ $authUser->profile_link }}" class="avatar-img rounded-circle" alt="User">
                        </div>

                        <div class="user-text">
                            <h6>{{ ucfirst($authUser->name) }}</h6>
                            <p class="text-muted mb-0">{{ ucfirst($authUser->role_name) }}</p>
                        </div>
                    </div>

                    <a class="dropdown-item" href="{{ role_route('admin.account.settings') }}">
                        Account Settings
                    </a>

                    {{-- Logout --}}
                    <a class="dropdown-item"
                       href="{{ role_route('auth.logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>

                    <form id="logout-form" action="{{ role_route('auth.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>

                </div>
            </li>
        </ul>
    </div>

    {{-- Sidebar --}}
    @include('layouts.admin.partials.sidebar')

    {{-- Page Content --}}
    <div class="page-wrapper">
        @yield('content')
    </div>

</div>

{{-- Scripts --}}
<script src="{{ asset('admin/assets/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('admin/assets/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.20.0/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.20.0/dist/additional-methods.min.js"></script>
<script src="{{ asset('admin/assets/js/script.js') }}"></script>

{{-- Livewire modal listeners --}}
<script>
    document.addEventListener('livewire:initialized', () => {

        Livewire.on('open-modal', (event) => {
            $('#' + event.name).modal('show');
        });

        Livewire.on('close-modal', (event) => {
            $('#' + event.name).modal('hide');
        });

    });
</script>

@stack('js')
@livewireScripts
@stack('livewire-js')
</body>
</html>
