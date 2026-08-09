@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between flex-col sm:flex-row gap-3">
        <div class="text-xs text-slate-800 font-bold">
            {!! __('Menampilkan') !!}
            <span class="font-extrabold text-slate-900">{{ $paginator->firstItem() }}</span>
            {!! __('sampai') !!}
            <span class="font-extrabold text-slate-900">{{ $paginator->lastItem() }}</span>
            {!! __('dari') !!}
            <span class="font-extrabold text-emerald-700">{{ $paginator->total() }}</span>
            {!! __('data') !!}
        </div>

        <div class="flex items-center gap-1.5 overflow-x-auto max-w-full py-1">
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 text-slate-400 bg-slate-200 rounded-lg text-xs font-bold cursor-not-allowed">
                    <i class="ri-arrow-left-s-line"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 text-slate-800 bg-white hover:bg-emerald-50 hover:text-emerald-800 border border-slate-300 rounded-lg text-xs font-bold transition">
                    <i class="ri-arrow-left-s-line"></i>
                </a>
            @endif

            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
                $pagesToDisplay = [];
                for ($i = 1; $i <= min(3, $lastPage); $i++) { $pagesToDisplay[] = $i; }
                for ($i = max(1, $currentPage - 1); $i <= min($lastPage, $currentPage + 1); $i++) { $pagesToDisplay[] = $i; }
                for ($i = max(1, $lastPage - 2); $i <= $lastPage; $i++) { $pagesToDisplay[] = $i; }
                $pagesToDisplay = array_unique($pagesToDisplay);
                sort($pagesToDisplay);
            @endphp

            @php $prevNum = 0; @endphp
            @foreach ($pagesToDisplay as $page)
                @if ($prevNum > 0 && $page - $prevNum > 1)
                    <span class="px-2 py-1 text-slate-500 font-bold text-xs select-none">&middot;&middot;&middot;</span>
                @endif

                @if ($page == $currentPage)
                    <span class="px-3.5 py-1.5 bg-emerald-600 text-white font-extrabold text-xs rounded-lg shadow-sm">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $paginator->url($page) }}" class="px-3.5 py-1.5 bg-white hover:bg-emerald-50 text-slate-800 hover:text-emerald-800 border border-slate-300 rounded-lg text-xs font-bold transition">
                        {{ $page }}
                    </a>
                @endif

                @php $prevNum = $page; @endphp
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 text-slate-800 bg-white hover:bg-emerald-50 hover:text-emerald-800 border border-slate-300 rounded-lg text-xs font-bold transition">
                    <i class="ri-arrow-right-s-line"></i>
                </a>
            @else
                <span class="px-3 py-1.5 text-slate-400 bg-slate-200 rounded-lg text-xs font-bold cursor-not-allowed">
                    <i class="ri-arrow-right-s-line"></i>
                </span>
            @endif
        </div>
    </nav>
@endif
