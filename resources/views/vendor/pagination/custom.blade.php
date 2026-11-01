@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" style="display: flex; align-items: center; justify-content: space-between; width: 100%; font-size: 13px; color: #64748B;">
        
        <!-- Counter Text -->
        <div style="font-weight: 500;">
            Menampilkan {{ $paginator->firstItem() ?? 0 }}-{{ $paginator->lastItem() ?? 0 }} dari {{ $paginator->total() }} entri
        </div>

        <!-- Page Links -->
        <div style="display: flex; align-items: center; gap: 4px;">
            
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 1px solid #E2E8F0; background: #F8FAFC; color: #CBD5E1; cursor: not-allowed; font-size: 14px; font-weight: 700;">
                    ‹
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 1px solid #CBD5E1; background: #FFFFFF; color: #334155; text-decoration: none; font-size: 14px; font-weight: 700;" title="Halaman Sebelumnya">
                    ‹
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; color: #94A3B8;">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 10px; border-radius: 8px; background: #9A3400; color: #FFFFFF; font-weight: 800; border: 1px solid #9A3400;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" style="display: inline-flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 10px; border-radius: 8px; border: 1px solid #CBD5E1; background: #FFFFFF; color: #334155; text-decoration: none; font-weight: 600;">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 1px solid #CBD5E1; background: #FFFFFF; color: #334155; text-decoration: none; font-size: 14px; font-weight: 700;" title="Halaman Selanjutnya">
                    ›
                </a>
            @else
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: 1px solid #E2E8F0; background: #F8FAFC; color: #CBD5E1; cursor: not-allowed; font-size: 14px; font-weight: 700;">
                    ›
                </span>
            @endif

        </div>

    </nav>
@endif
