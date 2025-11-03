@props(['paginator'])

@if ($paginator->hasPages())
    <div class="pagination dashboard-pagination mt-md-3 mt-0 mb-4">
        <ul>
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li><span class="page-link prev disabled">Prev</span></li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" class="page-link prev" rel="prev">Prev</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($paginator->links()->elements[0] ?? [] as $page => $url)
                @if ($page == $paginator->currentPage())
                    <li><span class="page-link active">{{ $page }}</span></li>
                @else
                    <li><a href="{{ $url }}" class="page-link">{{ $page }}</a></li>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" class="page-link next" rel="next">Next</a>
                </li>
            @else
                <li><span class="page-link next disabled">Next</span></li>
            @endif
        </ul>
    </div>
@endif
