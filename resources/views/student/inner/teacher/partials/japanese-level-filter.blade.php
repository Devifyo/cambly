<div class="accordion-item border-bottom">
    <div class="accordion-header" id="headingJapaneseLevel">
        <div class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapseJapaneseLevel" aria-controls="collapseJapaneseLevel" role="button">
            <div class="d-flex align-items-center w-100">
                <h5>Japanese Level</h5>
                <div class="ms-auto">
                    <span><i class="fas fa-chevron-down"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div id="collapseJapaneseLevel" class="accordion-collapse show" aria-labelledby="headingJapaneseLevel">
        <div class="accordion-body pt-3">

            <select class="form-select japanese-level-filter" name="japanese_level" id="japaneseLevel" aria-label="Select Japanese Level">
                {{-- Default Option --}}
                <option value="" {{ is_null(request('japanese_level')) ? 'selected' : '' }}>Any Level</option>
                
                {{-- Iterate options --}}
                @foreach(getJapaneseLevelName() as $level)
                    <option 
                        value="{{ $level }}" 
                        {{ request('japanese_level') == $level ? 'selected' : '' }}
                    >
                        {{ ucfirst($level) }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>