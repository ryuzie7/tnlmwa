@if ($paginator->hasPages())
    <nav role="navigation" class="flex justify-center mt-6" aria-label="Pagination Navigation">
        <ul class="inline-flex items-center -space-x-px">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="px-3 py-2 text-gray-400">Prev</li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">Prev</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @php
                $total = $paginator->lastPage();
                $current = $paginator->currentPage();
            @endphp

            {{-- Always show first 3 pages --}}
            @for ($i = 1; $i <= min(3, $total); $i++)
                <li>
                    <a href="{{ $paginator->url($i) }}"
                       class="px-3 py-2 border {{ $i == $current ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                        {{ $i }}
                    </a>
                </li>
            @endfor

            {{-- Ellipsis if not near the end --}}
            @if ($current > 4 && $current < $total - 2)
                <li class="px-3 py-2 text-gray-500">...</li>
            @endif

            {{-- Always show last page --}}
            @if ($total > 3)
                <li>
                    <a href="{{ $paginator->url($total) }}"
                       class="px-3 py-2 border {{ $current == $total ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                        {{ $total }}
                    </a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">Next</a>
                </li>
            @else
                <li class="px-3 py-2 text-gray-400">Next</li>
            @endif
        </ul>
    </nav>
@endif
