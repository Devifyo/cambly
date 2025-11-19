@extends('layouts.admin.app')

{{-- Set the title for this page --}}
@section('title', 'Student Listing')

{{-- Include necessary CSS for styling, assuming your admin CSS is loaded via app.blade.php or similar --}}
@push('css')
    <link rel="stylesheet" href="{{ asset('admin/assets/plugins/datatables/datatables.min.css') }}">
@endpush

@section('content')
    <livewire:admin.teacher-listing />
@endsection