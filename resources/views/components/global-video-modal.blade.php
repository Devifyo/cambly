{{-- 
    HTML STRUCTURE 
    This is the hidden modal that sits at the bottom of the page waiting to be called.
--}}
<div class="modal fade" id="globalVideoModal" tabindex="-1" aria-labelledby="globalVideoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="globalVideoModalLabel">Video Preview</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                {{-- 16:9 Aspect Ratio Container --}}
                <div class="ratio ratio-16x9">
                    <iframe id="global-video-iframe" src="" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 
    JAVASCRIPT LOGIC
    We use @push('scripts') so this JS is pushed to the bottom of your layout file 
    where your other scripts are loaded.
--}}
@push('scripts')
<script>
    /**
     * Global function to open the video modal.
     * Can be called from anywhere: openVideoPreview('https://youtube.com/...')
     */
    window.openVideoPreview = function(url) {
        if (!url) return;

        // 1. Extract YouTube ID using Regex
        let videoId = null;
        // Handles: youtube.com/watch?v=, m.youtube.com, youtu.be/, youtube.com/embed/
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        const match = url.match(regExp);

        if (match && match[2].length === 11) {
            videoId = match[2];
        } else {
            alert('Invalid YouTube URL provided.');
            return;
        }

        // 2. Construct URL (Autoplay on)
        const embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1`;

        // 3. Find elements
        const modalEl = document.getElementById('globalVideoModal');
        const iframe = document.getElementById('global-video-iframe');

        // 4. Set source and Show
        if (iframe && modalEl) {
            iframe.src = embedUrl;
            
            // Bootstrap 5 Modal instantiation
            const bsModal = new bootstrap.Modal(modalEl);
            bsModal.show();
        }
    };

    /**
     * Event Listener: Stop Video on Close
     * When the modal is hidden, remove the source to stop audio/video playback.
     */
    document.addEventListener('DOMContentLoaded', () => {
        const modalEl = document.getElementById('globalVideoModal');
        if (modalEl) {
            modalEl.addEventListener('hidden.bs.modal', function () {
                document.getElementById('global-video-iframe').src = "";
            });
        }
    });
</script>
@endpush