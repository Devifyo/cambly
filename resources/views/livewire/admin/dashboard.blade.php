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
        {{-- Widget 1: Total Teachers --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-primary border-primary">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </span>
                        <div class="dash-count">
                            <h3>{{ $totalTeachers }}</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info">
                        <h6 class="text-muted">Teachers</h6>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Widget 2: Total Students --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-success">
                            <i class="fas fa-user-graduate"></i>
                        </span>
                        <div class="dash-count">
                            <h3>{{ $totalStudents }}</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info">
                        <h6 class="text-muted">Students</h6>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Widget 3: Completed Lessons --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-danger border-danger">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </span>
                        <div class="dash-count">
                            <h3>{{ $totalCompletedLessons }}</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info">
                        <h6 class="text-muted">Completed Lessons</h6>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Widget 4: Active Subscriptions --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-warning border-warning">
                            <i class="fe fe-credit-card"></i>
                        </span>
                        <div class="dash-count">
                            <h3>{{ $totalActiveSubscriptions }}</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info">
                        <h6 class="text-muted">Active Subscriptions</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        {{-- Recent Teacher List --}}
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
                                    <th>Gender</th>
                                    <th>Joined at</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentTeachers as $teacher)
                                <tr>
                                    <td>
                                        <h2 class="table-avatar">
                                            <a href="#" class="avatar avatar-sm me-2">
                                                <img class="avatar-img rounded-circle" src="{{ $teacher->profile_link }}" alt="User Image">
                                            </a>
                                            <a href="#">{{ $teacher->name }}</a>
                                        </h2>
                                    </td>
                                    <td>{{ ucfirst($teacher->gender ?? 'N/A') }}</td>
                                    <td>{{ $teacher->created_at->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">No recent teachers found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-center">
                     <a href="{{ route('admin.teachers.index') }}" class="text-primary">View All Teachers</a>
                </div>
            </div>
        </div>
        
        {{-- Recent Students List --}}
        <div class="col-md-6 d-flex">
            <div class="card card-table flex-fill">
                <div class="card-header">
                    <h4 class="card-title">Recent Students </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-center mb-0">
                            <thead>
                                <tr> 
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Joined at</th> 
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentStudents as $student)
                                <tr>
                                    <td>
                                        <h2 class="table-avatar">
                                            <a href="#" class="avatar avatar-sm me-2">
                                                <img class="avatar-img rounded-circle" src="{{ $student->profile_link }}" alt="User Image">
                                            </a>
                                            <a href="#">{{ $student->name }}</a>
                                        </h2>
                                    </td>
                                    <td>{{ $student->email }}</td>
                                    <td>{{ $student->created_at->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">No recent students found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-center">
                     <a href="{{ route('admin.students.index') }}" class="text-primary">View All Students</a>
                </div>
            </div>
        </div>
    </div>
</div>