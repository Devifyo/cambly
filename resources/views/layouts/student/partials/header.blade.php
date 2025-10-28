<header class="header header-custom header-fixed inner-header relative">
    <div class="container">
        <nav class="navbar navbar-expand-lg header-nav">
            <div class="navbar-header">
                <a id="mobile_btn" href="javascript:void(0);">
                    <span class="bar-icon"><span></span><span></span><span></span></span>
                </a>
                <a href="#" class="navbar-brand logo">
                    <span class="logo-text">Cambly</span>
                </a>
            </div>

            <div class="main-menu-wrapper">
                <div class="menu-header">
                    <a href="#" class="menu-logo">
                        <img src="{{ asset('assets/img/logo.svg') }}" class="img-fluid" alt="Logo">
                    </a>
                    <a id="menu_close" class="menu-close" href="javascript:void(0);">
                        <i class="fas fa-times"></i>
                    </a>
                </div>

                <ul class="main-nav">
                    <li class="active"><a href="#">Dashboard</a></li>

                    <li class="has-submenu active">
                        <a href="javascript:void(0);">Manage Meetings <i class="fas fa-chevron-down"></i></a>
                        <ul class="submenu">
                            <li class="active"><a href="#">Meetings</a></li>
                            <li class="has-submenu">
                                <a href="javascript:void(0);">Your Meetings</a>
                                <ul class="submenu inner-submenu">
                                    <li><a href="#">Upcoming</a></li>
                                    <li><a href="#">Completed</a></li>
                                </ul>
                            </li>
                            <li><a href="#">Favourites</a></li>
                        </ul>
                    </li>

                    <li><a href="#">Search Teacher</a></li>
                    <li><a href="#">Profile Settings</a></li>
                </ul>
            </div>

            <div class="header-menu">
                <ul class="nav header-navbar-rht">
                    <!-- Search -->
                    {{-- <li class="searchbar">
                        <a href="javascript:void(0);"><i class="feather-search"></i></a>
                        <div class="togglesearch">
                            <form action="#">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search...">
                                    <button type="submit" class="btn">Search</button>
                                </div>
                            </form>
                        </div>
                    </li> --}}

                    <!-- Theme Toggle -->
                    {{-- <li class="header-theme noti-nav">
                        <a href="javascript:void(0);" id="dark-mode-toggle" class="theme-toggle">
                            <i class="isax isax-sun-1"></i>
                        </a>
                        <a href="javascript:void(0);" id="light-mode-toggle" class="theme-toggle activate">
                            <i class="isax isax-moon"></i>
                        </a>
                    </li> --}}

                    <!-- Notifications -->
                    <li class="nav-item dropdown noti-nav me-3 pe-0">
                        <a href="#" class="dropdown-toggle active-dot active-dot-danger nav-link p-0" data-bs-toggle="dropdown">
                            <i class="isax isax-notification-bing"></i>
                        </a>
                        <div class="dropdown-menu notifications dropdown-menu-end">
                            <div class="topnav-dropdown-header">
                                <span class="notification-title">Notifications</span>
                            </div>
                            <div class="noti-content">
                                <ul class="notification-list">
                                    <li class="notification-message">
                                        <a href="#">
                                            <div class="notify-block d-flex">
                                                <span class="avatar">
                                                    <img class="avatar-img" alt="" src="{{ asset('assets/img/clients/client-01.jpg') }}">
                                                </span>
                                                <div class="media-body">
                                                    <h6>Travis Tremble <span class="notification-time">18.30 PM</span></h6>
                                                    <p class="noti-details">Sent a amount of $210 for his Appointment <span class="noti-title">Dr.Ruby perin</span></p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <!-- Repeat other 3 notifications exactly as in original -->
                                </ul>
                            </div>
                        </div>
                    </li>

                    <!-- Messages -->
                    {{-- <li class="nav-item noti-nav me-3 pe-0">
                        <a href="#" class="dropdown-toggle nav-link active-dot active-dot-success p-0">
                            <i class="isax isax-message-2"></i>
                        </a>
                    </li> --}}

                    <!-- Cart -->
                    {{-- <li class="nav-item dropdown noti-nav view-cart-header me-3 pe-0">
                        <a href="#" class="dropdown-toggle nav-link active-dot active-dot-purple p-0 position-relative" data-bs-toggle="dropdown">
                            <i class="isax isax-shopping-cart"></i>
                        </a>
                        <div class="dropdown-menu notifications dropdown-menu-end">
                            <div class="shopping-cart">
                                <ul class="shopping-cart-items list-unstyled">
                                    <!-- 3 cart items – copy from original -->
                                </ul>
                                <div class="booking-summary pt-3">
                                    <div class="booking-item-wrap">
                                        <ul class="booking-date">
                                            <li>Subtotal <span>$5,877.00</span></li>
                                            <li>Shipping <span>$25.00</span></li>
                                            <li>Tax <span>$0.00</span></li>
                                            <li>Total <span>$5,2555</span></li>
                                        </ul>
                                        <div class="booking-total">
                                            <ul class="booking-total-list text-align">
                                                <li><div class="clinic-booking pt-3"><a class="apt-btn" href="#">View Cart</a></div></li>
                                                <li><div class="clinic-booking pt-3"><a class="apt-btn" href="#">Checkout</a></div></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li> --}}

                    <!-- User Menu -->
                    <li class="nav-item dropdown has-arrow logged-item">
                        <a href="#" class="nav-link ps-0" data-bs-toggle="dropdown">
                            <span class="user-img">
                                <img class="rounded-circle" src="{{ asset('assets/img/doctors-dashboard/profile-06.jpg') }}" width="31" alt="User">
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div class="user-header">
                                <div class="avatar avatar-sm">
                                    <img src="{{ asset('assets/img/doctors-dashboard/profile-06.jpg') }}" alt="User Image" class="avatar-img rounded-circle">
                                </div>
                                <div class="user-text">
                                    <h6>Hendrita Hayes</h6>
                                    <p class="text-muted mb-0">Student</p>
                                </div>
                            </div>
                            <a class="dropdown-item" href="#">Dashboard</a>
                            <a class="dropdown-item" href="#">Profile Settings</a>
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
