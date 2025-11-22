@extends('layouts.admin.app')

{{-- Set the title for this page --}}
@section('title', 'Subadmin Management')

@push('css')
    {{-- Using the same plugins as your other pages --}}
    <link rel="stylesheet" href="{{ asset('admin/assets/plugins/datatables/datatables.min.css') }}">
@endpush

@section('content')
    <livewire:admin.subadmin-listing />
@endsection