<div class="form-group">
    <label class="form-label" for="mother_tongue">Mother Tongue <span class="text-danger">*</span></label>
    
    <select id="mother_tongue" name="mother_tongue" class="form-control" required
            data-preselected="{{ $selected }}">
        {{-- JS will populate options --}}
    </select>
    
    @error('mother_tongue')
        <div class="text-danger mt-1"><small>{{ $message }}</small></div>
    @enderror
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        const $mtSelect = $('#mother_tongue');
        
        // 1. Populate Options
        if ($mtSelect.children('option').length === 0) {
            const allCodes = ISO6391.getAllCodes();
            $mtSelect.append(new Option('', '', false, false)); // Placeholder
            
            allCodes.forEach(code => {
                $mtSelect.append(new Option(ISO6391.getName(code), code, false, false));
            });
        }

        // 2. Initialize Select2
        $mtSelect.select2({
            placeholder: "Select Mother Tongue",
            width: '100%',
            allowClear: true
        });

        // 3. Set Selected Value
        const preselected = $mtSelect.data('preselected');
        if (preselected) {
            $mtSelect.val(preselected).trigger('change');
        }
    });
</script>
@endpush