@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between flex-col sm:flex-row gap-4">
        <div class="text-sm text-slate-600 font-medium">
            {!! __('Menampilkan') !!}
            <span class="font-bold text-slate-900">{{ $paginator->firstItem() }}</span>
            {!! __('sampai') !!}
            <span class="font-bold text-slate-900">{{ $paginator->lastItem() }}</span>
            {!! __('dari') !!}
            <span class="font-bold text-teal-700">{{ $paginator->total() }}</span>
            {!! __('data') !!}
        </div>

        <div class="flex items-center gap-1.5 overflow-x-auto max-w-full py-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-2 text-slate-400 bg-slate-100 rounded-xl text-sm font-semibold cursor-not-allowed">
                    <i class="ri-arrow-left-s-line"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 text-slate-700 bg-white hover:bg-teal-50 hover:text-teal-700 border border-slate-300 rounded-xl text-sm font-semibold transition">
                    <i class="ri-arrow-left-s-line"></i>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();

                // Define window range (3 start, 3 end, and current page neighbors)
                $pagesToDisplay = [];

                // Always include 1, 2, 3
                for ($i = 1; $i <= min(3, $lastPage); $i++) {
                    $pagesToDisplay[] = $i;
                }

                // Include current page and immediate neighbors
                for ($i = max(1, $currentPage - 1); $i <= min($lastPage, $currentPage + 1); $i++) {
                    $pagesToDisplay[] = $i;
                }

                // Always include last 3 pages (e.g. 14, 15, 16)
                for ($i = max(1, $lastPage - 2); $i <= $lastPage; $i++) {
                    $pagesToDisplay[] = $i;
                }

                $pagesToDisplay = array_unique($pagesToDisplay);
                sort($pagesToDisplay);
            @endphp

            @php $prevNum = 0; @endphp
            @foreach ($pagesToDisplay as $page)
                @if ($prevNum > 0 && $page - $prevNum > 1)
                    <span class="px-2.5 py-1.5 text-slate-400 text-sm font-bold select-none">...</span>
                @endif

                @if ($page == $currentPage)
                    <span class="px-3.5 py-2 bg-teal-600 text-white font-bold text-sm rounded-xl shadow-md shadow-teal-600/30">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $paginator->url($page) }}" class="px-3.5 py-2 bg-white hover:bg-teal-50 text-slate-700 hover:text-teal-700 border border-slate-300 rounded-xl text-sm font-medium transition">
                        {{ $page }}
                    </a>
                @endif

                @php $prevNum = $page; @endphp
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 text-slate-700 bg-white hover:bg-teal-50 hover:text-teal-700 border border-slate-300 rounded-xl text-sm font-semibold transition">
                    <i class="ri-arrow-right-s-line"></i>
                </a>
            @else
                <span class="px-3 py-2 text-slate-400 bg-slate-100 rounded-xl text-sm font-semibold cursor-not-allowed">
                    <i class="ri-arrow-right-s-line"></i>
                </span>
            @endif
        </div>
    </nav>
@endif
