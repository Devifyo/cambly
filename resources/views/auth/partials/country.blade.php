<div class="form-floating">
    <select class="form-select" id="country_residence" name="country_residence" required>
        <option value="">Select Country</option>
    </select>
    <label for="country_residence">Country</label>
    <span class="input-group-text-icon"><i class="fa-solid fa-location-dot"></i></span>
</div>

@push('scripts')
<script>
$(document).ready(function () {
    // Fetch Countries from RestCountries API
    fetch('https://restcountries.com/v3.1/all?fields=name,cca2')
        .then(res => res.json())
        .then(data => {
            // Sort alphabetically
            data.sort((a, b) => a.name.common.localeCompare(b.name.common));
            
            const $select = $('#country_residence');
            data.forEach(c => {
                $select.append(new Option(c.name.common, c.name.common));
            });
        })
        .catch(err => {
            console.error("Country API Error", err);
            // Fallback options if API fails
            $('#country_residence').append(new Option('United States', 'United States'));
            $('#country_residence').append(new Option('United Kingdom', 'United Kingdom'));
        });
});
</script>
@endpush