@extends('layouts.admin.app')

{{-- Page Title --}}
@section('title', 'Role & Permission Management')

{{-- Optional: Add specific CSS if needed --}}
@push('css')
@endpush

@section('content')
    {{-- CALL THE LIVEWIRE COMPONENT HERE --}}
    <livewire:admin.manage-permission />
@endsection

@push('js')
@endpush