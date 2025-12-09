function copyDiscord(btn, text) {
        // 1. Copy text to clipboard
        navigator.clipboard.writeText(text).then(() => {
            
            // 2. Cache elements for easy access
            const icon = btn.querySelector('i');
            const span = btn.querySelector('.copy-text');
            
            // 3. Visual Feedback: Change Icon to Check & Color to Green
            btn.classList.remove('btn-light', 'text-muted');
            btn.classList.add('btn-success', 'text-white'); // Make button green
            
            icon.classList.remove('fa-regular', 'fa-copy');
            icon.classList.add('fa-solid', 'fa-check'); // Change icon to checkmark
            
            span.classList.remove('d-none'); // Show "Copied!" text
            
            // 4. Revert back after 2 seconds
            setTimeout(() => {
                btn.classList.remove('btn-success', 'text-white');
                btn.classList.add('btn-light', 'text-muted');
                
                icon.classList.remove('fa-solid', 'fa-check');
                icon.classList.add('fa-regular', 'fa-copy');
                
                span.classList.add('d-none');
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy: ', err);
        });
}