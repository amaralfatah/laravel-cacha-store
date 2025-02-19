{{-- vendor/pagination/payne.blade.php --}}
@if ($paginator->hasPages())
    <nav class="pagination-wrap">
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="disabled"><span class="page-prev"><i class="fa fa-angle-double-left"></i></span></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}" class="page-prev" rel="prev"><i class="fa fa-angle-double-left"></i></a></li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li><span class="dot">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li><span class="page-number current">{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}" class="page-number">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" class="page-next" rel="next"><i class="fa fa-angle-double-right"></i></a></li>
            @else
                <li class="disabled"><span class="page-next"><i class="fa fa-angle-double-right"></i></span></li>
            @endif
        </ul>
    </nav>
@endif
