<div class="form-group">
    <label class="form-label" for="native_languages">Native Language (Max 3) <span class="text-danger">*</span></label>
    
    <select id="native_languages" name="native_languages[]" class="form-control" multiple="multiple" required
            data-preselected='@json($selected)'>
        {{-- JS will populate options --}}
    </select>
    
    @error('native_languages')
        <div class="text-danger mt-1"><small>{{ $message }}</small></div>
    @enderror
    @error('native_languages.*') {{-- Catch individual array errors --}}
        <div class="text-danger mt-1"><small>{{ $message }}</small></div>
    @enderror
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        const $nlSelect = $('#native_languages');
        
        // 1. Populate Options
        if ($nlSelect.children('option').length === 0) {
            const allCodes = ISO6391.getAllCodes();
            allCodes.forEach(code => {
                $nlSelect.append(new Option(ISO6391.getName(code), code, false, false));
            });
        }

        // 2. Initialize Select2
        $nlSelect.select2({
            placeholder: "Select Native Language(s)",
            width: '100%',
            allowClear: true,
            maximumSelectionLength: 3,
            language: {
                maximumSelected: function (e) { return "You can only select up to 3 languages."; }
            }
        });

        // 3. Set Selected Values
        const preselected = $nlSelect.data('preselected'); // This is an array
        if (preselected && preselected.length > 0) {
            $nlSelect.val(preselected).trigger('change');
        }
    });
</script>
@endpush