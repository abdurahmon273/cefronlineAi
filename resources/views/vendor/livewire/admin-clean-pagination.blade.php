@if ($paginator->hasPages())
    <nav class="d-flex justify-content-center mt-3" aria-label="Pagination Navigation">
        <ul class="pagination mb-0">
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link rounded-pill border-0 bg-light text-muted px-3">&lsaquo;</span>
                </li>
            @else
                <li class="page-item">
                    <button type="button" class="page-link rounded-pill border-0 bg-white text-dark px-3" wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="prev" aria-label="Previous">
                        &lsaquo;
                    </button>
                </li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link rounded-pill border-0 bg-transparent text-muted px-3">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link rounded-pill border-0 bg-dark text-white px-3">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <button type="button" class="page-link rounded-pill border-0 bg-white text-dark px-3" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" aria-label="Go to page {{ $page }}">
                                    {{ $page }}
                                </button>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <button type="button" class="page-link rounded-pill border-0 bg-white text-dark px-3" wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="next" aria-label="Next">
                        &rsaquo;
                    </button>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link rounded-pill border-0 bg-light text-muted px-3">&rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
