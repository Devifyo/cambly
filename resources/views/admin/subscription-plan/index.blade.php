@extends('layouts.admin.app')

{{-- Set the title for this page --}}
@section('title', 'Subscription Plans')

{{-- Push page-specific CSS --}}
@push('css')
    <link rel="stylesheet" href="{{ asset('admin/assets/plugins/datatables/datatables.min.css') }}">
    <style>
        textarea {
        background-color: transparent;
        border: 3px solid rgba(0, 0, 0, 0.3);
        border-radius: 6px;
        margin-bottom: 20px;
        height: 100px;
        resize: none;
        font-family: monospace;
        }

        textarea::placeholder { color: rgba(0, 0, 0, 0.4); }
        textarea:focus { outline: none; }
        textarea:focus::placeholder { color: transparent; }
    </style>
@endpush

{{-- This is the content that will be injected into the layout's @yield('content') --}}
@section('content')
    <livewire:admin.components.alert-handler />
    <livewire:admin.subscription-plans />
@endsection
