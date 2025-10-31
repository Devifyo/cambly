<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', config('app.name') . ' - Online Tutoring Platform')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{asset('assets/img/favicon.png')}}" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset('assets/img/apple-touch-icon.png')}}">

    <!-- Theme Settings Js -->
    <script src="{{asset('assets/js/theme-script.js')}}"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{asset('assets/plugins/fontawesome/css/fontawesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/plugins/fontawesome/css/all.min.css')}}">

    <!-- Iconsax CSS-->
    <link rel="stylesheet" href="{{asset('assets/css/iconsax.css')}}">

    <!-- Feathericon CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/feather.css')}}">

    <!-- Owl carousel CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/owl.carousel.min.css')}}">

    <!-- select CSS -->
    <link rel="stylesheet" href="{{asset('assets/plugins/select2/css/select2.min.css')}}">

    <!-- Datepicker CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap-datetimepicker.min.css')}}">

    <!-- Apex Css -->
    <link rel="stylesheet" href="{{asset('assets/plugins/apex/apexcharts.css')}}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/custom.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/newstyle.css')}}">

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
            <div class="container mt-4">
                @yield('content')
            </div>
        </div>
        <!-- /Page Content -->

        <!-- Footer -->
        @include('layouts.student.partials.footer')
        <!-- /Footer -->

    </div>
    <!-- /Main Wrapper -->

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
    <script src="{{asset('assets/js/owl.carousel.min.js')}}"></script>

    <!-- Apexchart JS -->
    <script src="{{asset('assets/plugins/apex/apexcharts.min.js')}}"></script>
    <script src="{{asset('assets/plugins/apex/chart-data.js')}}"></script>

    <!-- Datepicker JS -->
    <script src="{{asset('assets/js/moment.min.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap-datetimepicker.min.js')}}"></script>

    <!-- Circle Progress JS -->
    <script src="{{asset('assets/js/circle-progress.min.js')}}"></script>

    <!-- Custom JS -->
    <script src="{{asset('assets/js/script.js')}}"></script>

    @stack('scripts')
</body>
</html>
