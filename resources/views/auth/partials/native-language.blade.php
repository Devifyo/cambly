@push('styles')
<style>
    /* --- BEAUTIFUL TAGS (FLEXBOX FIX) --- */
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
        background-color: var(--primary-light) !important;
        border: 1px solid rgba(37, 99, 235, 0.2) !important;
        color: var(--primary-dark) !important;
        border-radius: 50px !important;
        padding: 4px 12px 4px 8px !important;
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: 0 !important;
        margin-bottom: 4px !important;
        display: inline-flex !important;
        align-items: center !important;
        flex-direction: row !important; 
    }

    /* The 'X' Button */
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice .select2-selection__choice__remove {
        border: none !important;
        background: transparent !important;
        color: var(--primary-dark) !important;
        padding: 0 !important;
        margin-right: 8px !important;
        font-size: 0 !important;
        width: 16px !important;
        height: 16px !important;
        display: flex !important;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        border-radius: 50%;
        opacity: 0.6;
        position: static !important; 
    }

    /* FontAwesome Icon for X */
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice .select2-selection__choice__remove::before {
        content: "\f00d" !important;
        font-family: "Font Awesome 6 Free" !important;
        font-weight: 900 !important;
        font-size: 0.75rem !important;
    }

    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice .select2-selection__choice__remove:hover {
        background-color: rgba(37, 99, 235, 0.2) !important;
        opacity: 1;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice .select2-selection__choice__remove{
        text-indent: 0 !important;
    }
</style>
@endpush

<div class="mb-4">
    <label class="form-label small fw-bold text-muted ms-1">Native Language (Max 3)</label>
    <div class="select2-wrapper">
        <select class="form-select" id="native_languages_select" multiple="multiple" style="width: 100%" 
                data-preselected='@json(old("native_languages") ? explode(",", old("native_languages")) : [])'>
        </select>
        
        <input type="hidden" name="native_languages" id="native_languages_hidden">

        <span class="input-group-text-icon">
            <i class="fa-regular fa-comments"></i>
        </span>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/iso-639-1@2.1.15/build/index.min.js"></script>
<script>
$(document).ready(function () {
    const $nlSelect = $('#native_languages_select');
    const $hiddenInput = $('#native_languages_hidden');

    // 1. Populate Options (Text = Name, Value = Code)
    if (typeof ISO6391 !== 'undefined') {
        // Get all codes
        const allCodes = ISO6391.getAllCodes();
        
        // Create an array of objects so we can sort by Name alphabetically
        const languages = allCodes.map(code => {
            return {
                code: code,
                name: ISO6391.getName(code)
            };
        });

        // Sort by Name
        languages.sort((a, b) => a.name.localeCompare(b.name));

        // Append Options: new Option(text, value)
        languages.forEach(lang => {
            $nlSelect.append(new Option(lang.name, lang.code, false, false));
        });
    }

    // 2. Initialize Select2
    $nlSelect.select2({
        theme: "bootstrap-5",
        placeholder: "Select languages",
        maximumSelectionLength: 3,
        allowClear: true,
        width: '100%',
        language: {
            maximumSelected: function (e) { return "You can only select up to 3 languages."; }
        }
    });

    // 3. Populate preselected data (Handles validation failure redirection)
    const preselected = $nlSelect.data('preselected');
    if (preselected && preselected.length > 0) {
        $nlSelect.val(preselected).trigger('change');
    }

    // 4. SYNC: Update hidden input when selection changes
    $nlSelect.on('change', function() {
        const selectedData = $(this).val(); // This will now be an array of CODES (e.g. ['en', 'fr'])
        
        // Join codes with comma (e.g. "en,fr")
        $hiddenInput.val(selectedData ? selectedData.join(',') : '');
        
        // Trigger validation on the hidden field
        if ($("#registerForm").validate) {
            $hiddenInput.valid(); 
        }
    });
});
</script>
@endpush