@props(['paginator'])

@php 
    $elements = $paginator->links()->elements[0] ?? []; 
@endphp

@if ($paginator->hasPages())
    <nav aria-label="Page navigation">
        <ul class="pagination">

            {{-- Previous Page --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">&laquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}">
                        &laquo;
                    </a>
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
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            {{-- Next Page --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}">
                        &raquo;
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">&raquo;</span>
                </li>
            @endif

        </ul>
    </nav>
@endif
