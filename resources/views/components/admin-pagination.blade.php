@props(['paginator'])

@php
    // In Livewire, we usually rely on the paginator object directly.
    // However, if you are extracting elements manually, ensure $page is the number.
    $elements = $paginator->links()->elements[0] ?? [];
@endphp

@if ($paginator->hasPages())
    <nav aria-label="Page navigation">
        <ul class="pagination">

            {{-- Previous Page --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link" aria-hidden="true">&laquo;</span>
                </li>
            @else
                <li class="page-item">
                    {{-- CHANGE: Use wire:click="previousPage" and remove href --}}
                    <button type="button" class="page-link" wire:click="previousPage" wire:loading.attr="disabled" rel="prev">
                        &laquo;
                    </button>
                </li>
            @endif

            {{-- Page Numbers --}}
            @foreach ($elements as $page => $url)
                @if ($page == $paginator->currentPage())
                    <li class="page-item active" aria-current="page">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        {{-- CHANGE: Use wire:click="gotoPage({{ $page }})" and remove href --}}
                        <button type="button" class="page-link" wire:click="gotoPage({{ $page }})">
                            {{ $page }}
                        </button>
                    </li>
                @endif
            @endforeach

            {{-- Next Page --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    {{-- CHANGE: Use wire:click="nextPage" and remove href --}}
                    <button type="button" class="page-link" wire:click="nextPage" wire:loading.attr="disabled" rel="next">
                        &raquo;
                    </button>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link" aria-hidden="true">&raquo;</span>
                </li>
            @endif

        </ul>
    </nav>
@endif