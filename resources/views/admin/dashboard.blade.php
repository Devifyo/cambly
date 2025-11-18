@extends('layouts.admin.app')

{{-- Set the title for this page --}}
@section('title', 'Dashboard')

{{-- This is the content that will be injected into the layout's @yield('content') --}}
@section('content')

<div class="content container-fluid">
    
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Welcome Admin!</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active">Dashboard</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-primary border-primary">
                            {{-- Changed icon --}}
                            <i class="fas fa-chalkboard-teacher"></i>
                        </span>
                        <div class="dash-count">
                            <h3>168</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info">
                        <h6 class="text-muted">Teachers</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-success">
                            {{-- Changed icon --}}
                            <i class="fas fa-user-graduate"></i>
                        </span>
                        <div class="dash-count">
                            <h3>487</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info">
                        
                        <h6 class="text-muted">Students</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-danger border-danger">
                            {{-- Changed icon --}}
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </span>
                        <div class="dash-count">
                            <h3>485</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info">
                        
                        <h6 class="text-muted">Completed Lessons</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-warning border-warning">
                            {{-- Changed icon --}}
                            <i class="fe fe-credit-card"></i>
                        </span>
                        <div class="dash-count">
                            <h3>3</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info">
                        
                        <h6 class="text-muted">Active Subscriptions</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="row">
        <div class="col-md-12 col-lg-6">
        
            <div class="card card-chart">
                <div class="card-header">
                    <h4 class="card-title">Revenue</h4>
                </div>
                <div class="card-body">
                    <div id="morrisArea"></div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6">
        
            <div class="card card-chart">
                <div class="card-header">
                    <h4 class="card-title">Status</h4>
                </div>
                <div class="card-body">
                    <div id="morrisLine"></div>
                </div>
            </div>
        </div>  
    </div> --}}
    <div class="row">
        <div class="col-md-6 d-flex">
        
            <div class="card card-table flex-fill">
                <div class="card-header">
                    <h4 class="card-title">Recent Teacher List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Country</th>
                                    <th>Gender</th>
                                    <th>Joined at</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <h2 class="table-avatar">
                                            <a href="profile.html" class="avatar avatar-sm me-2"><img class="avatar-img rounded-circle" src="{{ asset('admin/assets/img/profiles/avatar-01.jpg') }}" alt="User Image"></a>
                                            <a href="profile.html">Dr. Ruby Perrin</a>
                                        </h2>
                                    </td>
                                    <td>USA</td>
                                    <td>Female</td>
                                    <td>18 Nov 2023</td>
                                </tr>
                                <tr>
                                    <td>
                                        <h2 class="table-avatar">
                                            <a href="profile.html" class="avatar avatar-sm me-2"><img class="avatar-img rounded-circle" src="{{ asset('admin/assets/img/profiles/avatar-02.jpg') }}" alt="User Image"></a>
                                            <a href="profile.html">Dr. Darren Elder</a>
                                        </h2>
                                    </td>
                                    <td>Canada</td>
                                    <td>Male</td>
                                    <td>17 Nov 2023</td>
                                </tr>
                                <tr>
                                    <td>
                                        <h2 class="table-avatar">
                                            <a href="profile.html" class="avatar avatar-sm me-2"><img class="avatar-img rounded-circle" src="{{ asset('admin/assets/img/profiles/avatar-03.jpg') }}" alt="User Image"></a>
                                            <a href="profile.html">Dr. Deborah Angel</a>
                                        </h2>
                                    </td>
                                    <td>UK</td>
                                    <td>Female</td>
                                    <td>16 Nov 2023</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </div>
        <div class="col-md-6 d-flex">
            <div class="card  card-table flex-fill">
                <div class="card-header">
                    <h4 class="card-title">Recent Students </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-center mb-0">
                            <thead>
                                <tr>                                                    
                                    <th>Name</th>
                                    <th>Country</th>
                                    <th>Email</th>
                                    <th>Joined_at</th>                                                   
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <h2 class="table-avatar">
                                            <a href="profile.html" class="avatar avatar-sm me-2"><img class="avatar-img rounded-circle" src="{{ asset('admin/assets/img/profiles/avatar-04.jpg') }}" alt="User Image"></a>
                                            <a href="profile.html">Charlene Reed </a>
                                        </h2>
                                    </td>
                                    <td>USA</td>
                                    <td>student@mailinator.com</td>
                                    <td>20 Oct 2023</td>
                                </tr>
                                <tr>
                                    <td>
                                        <h2 class="table-avatar">
                                            <a href="profile.html" class="avatar avatar-sm me-2"><img class="avatar-img rounded-circle" src="{{ asset('admin/assets/img/profiles/avatar-05.jpg') }}" alt="User Image"></a>
                                            <a href="profile.html">Travis Trimble </a>
                                        </h2>
                                    </td>
                                    <td>UK</td>
                                    <td>travis@mailinator.com</td>
                                    <td>22 Oct 2023</td>
                                </tr>
                                <tr>
                                    <td>
                                        <h2 class="table-avatar">
                                            <a href="profile.html" class="avatar avatar-sm me-2"><img class="avatar-img rounded-circle" src="{{ asset('admin/assets/img/profiles/avatar-06.jpg') }}" alt="User Image"></a>
                                            <a href="profile.html">Carl Kelly</a>
                                        </h2>
                                    </td>
                                    <td>Australia</td>
                                    <td>carl@mailinator.com</td>
                                    <td>21 Oct 2023</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

@endsection

@push('js')
{{-- 
    If this page had specific JS files that aren't global, you would push them here.
    For example: <script src="{{ asset('admin/assets/js/page.specific.js') }}"></script>
    
    The Morris chart scripts are commented out in the HTML,
    so no need to push them here.
--}}
@endpush