@if (auth()->check())
    @include('layouts.student.partials.header-auth')
@else
    @include('layouts.student.partials.header-guest')
@endif