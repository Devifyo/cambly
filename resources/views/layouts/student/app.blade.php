<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', config('app.name') . ' - Online Tutoring Platform')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Theme Settings Js -->
    <script src="{{asset('assets/js/theme-script.js')}}"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{asset('assets/plugins/fontawesome/css/fontawesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/plugins/fontawesome/css/all.min.css')}}">

    <!-- Iconsax CSS-->
    <link rel="stylesheet" href="{{asset('assets/css/iconsax.css')}}">

    <!-- select CSS -->
    <link rel="stylesheet" href="{{asset('assets/plugins/select2/css/select2.min.css')}}">

    <!-- Apex Css -->
    {{-- <link rel="stylesheet" href="{{asset('assets/plugins/apex/apexcharts.css')}}"> --}}
    <link rel="stylesheet" href="{{asset('assets/css/feather.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap-datetimepicker.min.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/custom.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/newstyle.css')}}">
    <style>
        .header-navbar-rht .logged-item .user-img {
            width: 3rem; /* Adjust as needed */
            height: 3rem; /* Adjust as needed */
            display: inline-block;
            vertical-align: middle;
        }

        .header-navbar-rht .logged-item .user-img img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover; /* Ensures the image covers the circle without distortion */
        }

        .user-header .avatar {
            width: 3rem; /* Adjust as needed */
            height: 3rem; /* Adjust as needed */
            margin-right: 10px; /* Adjust spacing as needed */
        }

        .user-header .avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

    </style>
    @stack('styles')
</head>
<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <!-- Header -->
        @include('layouts.student.partials.header')
        <!-- /Header -->

        <!-- Page Content -->
        <div class="content d-flex justify-content-center align-items-center">
            <x-impersonation-banner />
            <div class="container mt-4">
                @if(!is_impersonating())
                    <x-subscription-status-alert />
                @endif
                @yield('content')
            </div>
        </div>
        <!-- /Page Content -->

        <!-- Footer -->
        @include('layouts.student.partials.footer')
        <!-- /Footer -->

    </div>
    <!-- /Main Wrapper -->
    <x-global-video-modal />
    <!-- Modals -->
    @stack('modals')

    <!-- jQuery -->
    <script src="{{asset('assets/js/jquery-3.7.1.min.js')}}"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{asset('assets/js/bootstrap.bundle.min.js')}}"></script>

    <!-- Sticky Sidebar JS -->
        <script src="{{asset('assets/plugins/theia-sticky-sidebar/ResizeSensor.js')}}"></script>
    <script src="{{asset('assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js')}}"></script>

    <!-- select JS -->
    <script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>

    <!-- Owl Carousel JS -->
    {{-- <script src="{{asset('assets/js/owl.carousel.min.js')}}"></script> --}}

    <!-- Apexchart JS -->
    {{-- <script src="{{asset('assets/plugins/apex/apexcharts.min.js')}}"></script>
    <script src="{{asset('assets/plugins/apex/chart-data.js')}}"></script> --}}

    <!-- Datepicker JS -->
    {{-- <script src="{{asset('assets/js/moment.min.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap-datetimepicker.min.js')}}"></script> --}}
    <script src="{{asset('assets/js/feather.min.js')}}"></script>
    <script src="{{asset('assets/js/moment.min.js')}}"></script>
    <script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>
    <!-- Circle Progress JS -->
    <script src="{{asset('assets/js/circle-progress.min.js')}}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{asset('assets/js/script.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.20.0/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.20.0/dist/additional-methods.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/iso-639-1@3.1.5/build/index.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput-jquery.min.js'></script>
    <script src="https://cdn.jsdelivr.net/gh/linuxguist/countries@main/script.js"></script>
    <script>
                // Set global defaults for jQuery Validation
        $.validator.setDefaults({
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('error');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('error').removeClass(validClass);
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('error').addClass(validClass);
            }
        });

    </script>    
    @stack('scripts')
</body>
</html>
