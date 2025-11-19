@extends('layouts.admin.app')

{{-- Set the title for this page --}}
@section('title', 'Dashboard')

{{-- This is the content that will be injected into the layout's @yield('content') --}}
@section('content')

<livewire:admin.dashboard />

@endsection

@push('js')
{{-- 
    If this page had specific JS files that aren't global, you would push them here.
    For example: <script src="{{ asset('admin/assets/js/page.specific.js') }}"></script>
    
    The Morris chart scripts are commented out in the HTML,
    so no need to push them here.
--}}
@endpush