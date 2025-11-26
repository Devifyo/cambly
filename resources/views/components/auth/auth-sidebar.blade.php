    <div class="col-lg-6 d-none d-lg-flex flex-column align-items-center justify-content-center bg-auth-image position-relative text-white p-5">
        <div class="bg-overlay"></div>
        <div class="z-index-2 position-relative text-center px-4">
            <h1 class="display-4 fw-bold mb-3 text-white">{{ $heading ?? 'Learn Without Limits' }}</h1>
            <p class="lead text-white-50 mb-4">{{ $description ?? 'Connect with expert teachers and master new skills today.' }}</p>
            
            <div class="d-inline-flex align-items-center bg-white bg-opacity-10 backdrop-blur rounded-pill px-4 py-2 border border-light border-opacity-25">
                <i class="fas fa-user-graduate me-2 text-warning"></i>
                <span class="small">Join 10,000+ Students</span>
            </div>
        </div>
        
        <div class="shape-circle-1"></div>
        <div class="shape-circle-2"></div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
    @endpush