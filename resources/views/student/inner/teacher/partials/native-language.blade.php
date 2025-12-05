<div class="accordion-item border-bottom">
    <div class="accordion-header" id="headingLanguage">
        <div class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapseLanguage" aria-controls="collapseLanguage" role="button">
            <div class="d-flex align-items-center w-100">
                <h5>Native Language</h5>
                <div class="ms-auto">
                    <span><i class="fas fa-chevron-down"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div id="collapseLanguage" class="accordion-collapse show" aria-labelledby="headingLanguage">
        <div class="accordion-body pt-3">
            @php
                $selectedLanguages = request('languages', []);
            @endphp
            {{-- Iterate through the languages from the DB --}}
            @foreach(getAllLanguages() as $language)
                <div class="form-check mb-2">
                    <input 
                        class="form-check-input language-filter" 
                        type="checkbox" 
                        name="language_filter[]" 
                        {{-- Ensure ID is unique by using the language ID --}}
                        id="lang_{{ $language->id }}"
                        {{-- IMPORTANT: Ensure this value matches what your scopeFilterByLanguage expects (name or code) --}}
                        value="{{ $language->code }}"
                        {{-- Check if this specific language name is in the selected array --}}
                        {{ in_array($language->code, $selectedLanguages ?? []) ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="lang_{{ $language->id }}">
                        {{ ucfirst($language->name) }}
                    </label>
                </div>
            @endforeach
        </div>
    </div>
</div>