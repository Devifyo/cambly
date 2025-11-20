@extends('layouts.admin.app')

{{-- Set the title for this page --}}
@section('title', 'Account Settings')

@section('content')
    {{-- Include the Livewire component for all the account logic --}}
    <livewire:admin.account-settings />
@endsection