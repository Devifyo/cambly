<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        {{-- Dynamic title, defaults to 'Admin' --}}
        <title>@yield('title', 'Admin') - {{config('app.name')}}</title>

        <meta name="description" content="The responsive professional Doccure template offers many features, like scheduling appointments with  top doctors, clinics, and hospitals via voice, video call & chat.">
        <meta name="keywords" content="practo clone, doccure, doctor appointment, Practo clone html template, doctor booking template">
        <meta name="author" content="Practo Clone HTML Template - Doctor Booking Template">
        
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('admin/assets/img/favicon.png') }}">
        
        <link rel="stylesheet" href="{{ asset('admin/assets/css/bootstrap.min.css') }}">
        
        <link rel="stylesheet" href="{{ asset('admin/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('admin/assets/plugins/fontawesome/css/all.min.css') }}">
        
        <link rel="stylesheet" href="{{ asset('admin/assets/css/feathericon.min.css') }}">
        
        <link rel="stylesheet" href="{{ asset('admin/assets/plugins/morris/morris.css') }}">
        
        <link rel="stylesheet" href="{{ asset('admin/assets/css/custom.css') }}">
        <style>
            .logo-text {
                background: linear-gradient(90deg, #0E82FD 0%, #06AED4 70%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                font-weight: 700;
                font-size: 24px; /* adjust as needed */
                display: inline-block;
            }

        </style>
        {{-- Add a stack for page-specific CSS --}}
        @stack('css')
    </head>
    <body>
    
        <div class="main-wrapper">
        
            <div class="header">
            
                <div class="header-left">
                    {{-- Use a named route for the dashboard --}}
                    <a href="{{ route('admin.dashboard') }}" class="logo">
                        <span class="logo-text">{{config('app.name')}}</span>
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="logo logo-small">
                        <span class="logo-text">{{config('app.name')}}</span>
                    </a>
                </div>
                <a href="javascript:void(0);" id="toggle_btn">
                    <i class="fe fe-text-align-left"></i>
                </a>
                
                <div class="top-nav-search">
                    <form>
                        <input type="text" class="form-control" placeholder="Search here">
                        <button class="btn" type="submit"><i class="fa fa-search"></i></button>
                    </form>
                </div>
                
                <a class="mobile_btn" id="mobile_btn">
                    <i class="fa fa-bars"></i>
                </a>
                <ul class="nav user-menu">

                    {{-- <li class="nav-item dropdown noti-dropdown">
                        <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                            <i class="fe fe-bell"></i> <span class="badge rounded-pill">3</span>
                        </a>
                        <div class="dropdown-menu notifications">
                            <div class="topnav-dropdown-header">
                                <span class="notification-title">Notifications</span>
                                <a href="javascript:void(0)" class="clear-noti"> Clear All </a>
                            </div>
                            <div class="noti-content">
                                <ul class="notification-list">
                                    <li class="notification-message">
                                        <a href="#">
                                            <div class="notify-block d-flex">
                                                <span class="avatar avatar-sm flex-shrink-0">
                                                    <img class="avatar-img rounded-circle" alt="User Image" src="{{ asset('admin/assets/img/doctors/doctor-thumb-01.jpg') }}">
                                                </span>
                                                <div class="media-body flex-grow-1">
                                                    <p class="noti-details"><span class="noti-title">Dr. Ruby Perrin</span> Schedule <span class="noti-title">her appointment</span></p>
                                                    <p class="noti-time"><span class="notification-time">4 mins ago</span></p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="topnav-dropdown-footer">
                                <a href="#">View all Notifications</a>
                            </div>
                        </div>
                    </li> --}}
                    <li class="nav-item dropdown has-arrow">
                        <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                            <span class="user-img"><img class="rounded-circle" src="{{ asset('admin/assets/img/profiles/avatar-01.jpg') }}" width="31" alt="Ryan Taylor"></span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="user-header">
                                <div class="avatar avatar-sm">
                                    <img src="{{ asset('admin/assets/img/profiles/avatar-01.jpg') }}" alt="User Image" class="avatar-img rounded-circle">
                                </div>
                                <div class="user-text">
                                    <h6>Ryan Taylor</h6>
                                    <p class="text-muted mb-0">Administrator</p>
                                </div>
                            </div>
                            <a class="dropdown-item" href="{{-- route('admin.profile') --}}">My Profile</a>
                            <a class="dropdown-item" href="{{-- route('admin.settings') --}}">Settings</a>
                            
                            {{-- Laravel Logout Link --}}
                            <a class="dropdown-item" href="{{ route('auth.logout') }}"
                               onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>

                        </div>
                    </li>
                    </ul>
                </div>
            <div class="sidebar" id="sidebar">
                <div class="sidebar-inner slimscroll">
                    <div id="sidebar-menu" class="sidebar-menu">
                        <ul>
                            <li class="menu-title"> 
                                <span>Main</span>
                            </li>
                            {{-- 
                                Use request()->is() to set the 'active' class 
                                (assuming your routes are prefixed with 'admin') 
                            --}}
                            <li class="{{ request()->is('admin/dashboard*') ? 'active' : '' }}"> 
                                <a href="{{ route('admin.dashboard') }}"><i class="fe fe-home"></i> <span>Dashboard</span></a>
                            </li>
                            <li class="{{ request()->is('admin/teachers*') ? 'active' : '' }}"> 
                                {{-- Update with your actual route --}}
                                <a href="{{-- route('admin.teachers.index') --}}"><i class="fe fe-user-plus"></i> <span>Teachers</span></a>
                            </li>
                            <li class="{{ request()->is('admin/students*') ? 'active' : '' }}"> 
                                {{-- Update with your actual route --}}
                                <a href="{{-- route('admin.students.index') --}}"><i class="fe fe-user"></i> <span>Students</span></a>
                            </li>
                            <li class="{{ request()->is('admin/subscriptions*') ? 'active' : '' }}"> 
                                {{-- Update with your actual route --}}
                                <a href="{{route('admin.subscription.plan.index')}}"><i class="fe fe-credit-card"></i> <span>Subscription Plans</span></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="page-wrapper">
                
                {{-- This is where the page-specific content will be injected --}}
                @yield('content')
            
            </div>
            </div>
        <script src="{{ asset('admin/assets/js/jquery-3.7.1.min.js') }}"></script>
        
        <script src="{{ asset('admin/assets/js/bootstrap.bundle.min.js') }}"></script>
        
        <script src="{{ asset('admin/assets/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
        {{-- <script src="{{ asset('admin/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('admin/assets/plugins/datatables/datatables.min.js') }}"></script> --}}
        {{-- These scripts are for the dashboard charts --}}
        {{-- <script src="{{ asset('admin/assets/plugins/raphael/raphael.min.js') }}"></script>    
        <script src="{{ asset('admin/assets/plugins/morris/morris.min.js') }}"></script>  
        <script src="{{ asset('admin/assets/js/chart.morris.js') }}"></script> --}}
        <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.20.0/dist/jquery.validate.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.20.0/dist/additional-methods.min.js"></script>
        <script  src="{{ asset('admin/assets/js/script.js') }}"></script>

        {{-- Add a stack for page-specific JavaScript --}}
        @stack('js')
        
    </body>
</html>