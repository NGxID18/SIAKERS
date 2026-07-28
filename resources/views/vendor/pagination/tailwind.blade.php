@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row items-center justify-between gap-4 py-3 text-slate-700">
        <!-- Results Summary Text -->
        <div class="text-xs sm:text-sm text-slate-500 font-medium">
            Menampilkan <span class="font-bold text-slate-800">{{ $paginator->firstItem() }}</span> sampai <span class="font-bold text-slate-800">{{ $paginator->lastItem() }}</span> dari <span class="font-bold text-teal-700">{{ $paginator->total() }}</span> data
        </div>

        <!-- Pagination Controls & Jump To Page -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- Navigation Links -->
            <div class="flex items-center gap-1 bg-slate-100 p-1.5 rounded-2xl border border-slate-200 shadow-xs">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-200/50 text-slate-400 cursor-not-allowed text-sm">
                        <i class="ri-arrow-left-s-line text-lg"></i>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white text-slate-700 hover:bg-teal-50 hover:text-teal-700 transition font-bold shadow-xs text-sm" title="Halaman Sebelumnya">
                        <i class="ri-arrow-left-s-line text-lg"></i>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="px-2 text-slate-400 text-xs font-bold">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="w-9 h-9 flex items-center justify-center rounded-xl bg-teal-600 text-white font-extrabold text-xs shadow-md shadow-teal-600/30">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white text-slate-700 hover:bg-teal-50 hover:text-teal-700 font-semibold transition shadow-xs text-xs">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white text-slate-700 hover:bg-teal-50 hover:text-teal-700 transition font-bold shadow-xs text-sm" title="Halaman Berikutnya">
                        <i class="ri-arrow-right-s-line text-lg"></i>
                    </a>
                @else
                    <span class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-200/50 text-slate-400 cursor-not-allowed text-sm">
                        <i class="ri-arrow-right-s-line text-lg"></i>
                    </span>
                @endif
            </div>

            <!-- Direct Jump to Page Form Input -->
            <form method="GET" action="{{ request()->url() }}" class="flex items-center gap-1.5 pl-2 border-l border-slate-200">
                @foreach(request()->query() as $key => $value)
                    @if($key !== 'page' && !is_array($value))
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <span class="text-xs text-slate-500 font-semibold hidden sm:inline">Ke Hal:</span>
                <input type="number" name="page" min="1" max="{{ $paginator->lastPage() }}" value="{{ $paginator->currentPage() }}" class="w-14 px-2 py-1.5 bg-white border border-slate-300 rounded-xl text-xs text-center font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500 shadow-xs" title="Ketik nomor halaman">
                <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition shadow-xs">
                    Go
                </button>
            </form>
        </div>
    </nav>
@endif
