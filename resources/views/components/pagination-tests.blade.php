@php
    $paginator = $paginator ?? $exercises ?? null;
    $hasPaginator = $paginator && method_exists($paginator, 'hasPages') && $paginator->hasPages();
@endphp

@if ($hasPaginator)
    <nav class="flex items-center justify-between bg-white px-4 py-4 sm:px-6 rounded-[20px]">
        <div class="flex flex-1 justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-500 cursor-not-allowed">
                    Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 border border-gray-300">
                    Previous
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative ml-3 inline-flex items-center rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 border border-gray-300">
                    Next
                </a>
            @else
                <span class="relative ml-3 inline-flex items-center rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-500 cursor-not-allowed">
                    Next
                </span>
            @endif
        </div>

        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Showing
                    <span class="font-medium">{{ $paginator->firstItem() ?? 0 }}</span>
                    to
                    <span class="font-medium">{{ $paginator->lastItem() ?? 0 }}</span>
                    of
                    <span class="font-medium">{{ $paginator->total() ?? 0 }}</span>
                    results
                </p>
            </div>
            <div>
                <div class="isolate inline-flex -space-x-px rounded-md shadow-sm">
                    @if ($paginator->onFirstPage())
                        <span class="relative inline-flex items-center px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 cursor-not-allowed rounded-l-md bg-white">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 rounded-l-md bg-white">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @php
                        $links = [];
                        $from = max($paginator->currentPage() - 1, 1);
                        $to = min($paginator->currentPage() + 1, $paginator->lastPage() ?? 1);

                        if ($from > 1) {
                            $links[] = ['page' => 1, 'url' => $paginator->url(1), 'active' => false];
                            if ($from > 2) {
                                $links[] = ['page' => '...', 'url' => null, 'active' => false];
                            }
                        }

                        for ($i = $from; $i <= $to; $i++) {
                            $links[] = ['page' => $i, 'url' => $paginator->url($i), 'active' => $i == $paginator->currentPage()];
                        }

                        if ($to < ($paginator->lastPage() ?? 1)) {
                            if ($to < ($paginator->lastPage() ?? 1) - 1) {
                                $links[] = ['page' => '...', 'url' => null, 'active' => false];
                            }
                            $links[] = ['page' => $paginator->lastPage() ?? 1, 'url' => $paginator->url($paginator->lastPage() ?? 1), 'active' => false];
                        }
                    @endphp

                    @foreach ($links as $link)
                        @if ($link['page'] === '...')
                            <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 bg-white">...</span>
                        @elseif ($link['active'])
                            <span class="relative z-10 inline-flex items-center bg-[#FD4D00] px-4 py-2 text-sm font-semibold text-white">
                                {{ $link['page'] }}
                            </span>
                        @else
                            <a href="{{ $link['url'] }}" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 bg-white">
                                {{ $link['page'] }}
                            </a>
                        @endif
                    @endforeach

                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 rounded-r-md bg-white">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span class="relative inline-flex items-center px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 cursor-not-allowed rounded-r-md bg-white">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </nav>
@endif
