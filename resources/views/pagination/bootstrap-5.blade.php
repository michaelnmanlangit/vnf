@if ($paginator->hasPages())
<div class="pagination-wrapper" style="display:flex !important;flex-direction:column !important;align-items:center !important;gap:.75rem;margin-top:2rem;padding:1.25rem 1rem;background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.1);">
    <div class="pagination-info" style="display:block !important;width:100% !important;text-align:center;font-size:.9rem;font-weight:500;color:#555;margin:0;">
        Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
    </div>
    
    <nav aria-label="Page navigation">
        <ul class="pagination" style="justify-content:center;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="disabled">
                    <span>&laquo; Previous</span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo; Previous</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="disabled">
                        <span>{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="active">
                                <span>{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next">Next &raquo;</a>
                </li>
            @else
                <li class="disabled">
                    <span>Next &raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
</div>
@endif
