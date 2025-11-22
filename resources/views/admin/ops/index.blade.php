@extends('layouts.admin.app')

@section('title', 'Ops Management')

@push('css')
    <link rel="stylesheet" href="{{ asset('admin/assets/plugins/datatables/datatables.min.css') }}">
@endpush

@section('content')
    <livewire:admin.ops-listing />
@endsection