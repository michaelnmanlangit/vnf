@if ($paginator->hasPages())
<div class="pagination-wrapper" style="display:flex !important;flex-direction:column !important;align-items:center !important;gap:.75rem;margin-top:2rem;padding:1.25rem 1rem;background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.1);">
    <div class="pagination-info" style="display:block !important;width:100% !important;text-align:center;font-size:.9rem;font-weight:500;color:#555;margin:0;">
        Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
    </div>
    
    <nav aria-label="Page navigation">
        <ul style="display:flex;gap:.5rem;flex-wrap:wrap;justify-content:center;align-items:center;list-style:none;padding:0;margin:0;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li style="display:inline-flex;">
                    <span style="display:flex;align-items:center;justify-content:center;padding:.5rem .85rem;border:1px solid #dee2e6;border-radius:6px;font-size:.9rem;color:#adb5bd;background:#fff;cursor:default;">&laquo; Previous</span>
                </li>
            @else
                <li style="display:inline-flex;">
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="display:flex;align-items:center;justify-content:center;padding:.5rem .85rem;border:1px solid #dee2e6;border-radius:6px;font-size:.9rem;color:#495057;background:#fff;text-decoration:none;transition:all .2s;">&laquo; Previous</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li style="display:inline-flex;">
                        <span style="display:flex;align-items:center;justify-content:center;padding:.5rem .85rem;border:1px solid #dee2e6;border-radius:6px;font-size:.9rem;color:#adb5bd;background:#fff;">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li style="display:inline-flex;">
                                <span style="display:flex;align-items:center;justify-content:center;padding:.5rem .85rem;border:1px solid #4169E1;border-radius:6px;font-size:.9rem;font-weight:600;color:#fff;background:#4169E1;min-width:38px;">{{ $page }}</span>
                            </li>
                        @else
                            <li style="display:inline-flex;">
                                <a href="{{ $url }}" style="display:flex;align-items:center;justify-content:center;padding:.5rem .85rem;border:1px solid #dee2e6;border-radius:6px;font-size:.9rem;color:#495057;background:#fff;text-decoration:none;min-width:38px;transition:all .2s;">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li style="display:inline-flex;">
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="display:flex;align-items:center;justify-content:center;padding:.5rem .85rem;border:1px solid #dee2e6;border-radius:6px;font-size:.9rem;color:#495057;background:#fff;text-decoration:none;transition:all .2s;">Next &raquo;</a>
                </li>
            @else
                <li style="display:inline-flex;">
                    <span style="display:flex;align-items:center;justify-content:center;padding:.5rem .85rem;border:1px solid #dee2e6;border-radius:6px;font-size:.9rem;color:#adb5bd;background:#fff;cursor:default;">Next &raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
</div>
@endif
