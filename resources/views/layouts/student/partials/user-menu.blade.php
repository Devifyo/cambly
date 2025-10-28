<li class="nav-item dropdown has-arrow logged-item">
    <a href="#" class="nav-link ps-0" data-bs-toggle="dropdown">
        <span class="user-img">
            <img class="rounded-circle" src="{{ auth()?->user()->avatar ?? asset('assets/img/students/student-default.jpg') }}" width="31" alt="{{ auth()?->user()?->name ?? 'Dave' }}">
        </span>
    </a>
    <div class="dropdown-menu dropdown-menu-end">
        <div class="user-header">
            <div class="avatar avatar-sm">
                <img src="{{ auth()?->user()?->avatar ?? asset('assets/img/students/student-default.jpg') }}" alt="User Image" class="avatar-img rounded-circle">
            </div>
            <div class="user-text">
                <h6>{{ auth()?->user()?->name ?? 'Dave' }}</h6>
                <p class="text-muted mb-0">Student</p>
            </div>
        </div>
        <a class="dropdown-item" href="">Dashboard</a>
        <a class="dropdown-item" href="">Profile Settings</a>
        <a class="dropdown-item" href="">My Sessions</a>
        <a class="dropdown-item" href="">Payment History</a>
        <form method="POST" action="{{route('auth.logout')}}">
            @csrf
            <button type="submit" class="dropdown-item">Logout</button>
        </form>
    </div>
</li
