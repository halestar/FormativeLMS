@if ($paginator->hasPages())
    <nav aria-label="Pagination">
        <ul class="pagination pagination-sm mb-0">
            <li class="page-item @if ($paginator->onFirstPage()) disabled @endif">
                @if ($paginator->onFirstPage())
                    <span class="page-link" aria-hidden="true">
                        <i class="bi bi-chevron-double-left"></i>
                    </span>
                @else
                    <button class="page-link" rel="first" aria-label="First page" wire:click="resetPage">
                        <i class="bi bi-chevron-double-left"></i>
                    </button>
                @endif
            </li>

            <li class="page-item @if ($paginator->onFirstPage()) disabled @endif">
                @if ($paginator->onFirstPage())
                    <span class="page-link" aria-hidden="true">
                        <i class="bi bi-chevron-left"></i>
                    </span>
                @else
                    <button class="page-link" wire:click="previousPage" rel="prev" aria-label="Previous page">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                @endif
            </li>

            <li class="page-item active" aria-current="page">
                <span class="page-link">{{ $paginator->currentPage() }}</span>
            </li>

            <li class="page-item @if (! $paginator->hasMorePages()) disabled @endif">
                @if (! $paginator->hasMorePages())
                    <span class="page-link" aria-hidden="true">
                        <i class="bi bi-chevron-right"></i>
                    </span>
                @else
                    <button class="page-link" wire:click="nextPage" rel="next" aria-label="Next page">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                @endif
            </li>

            <li class="page-item @if (! $paginator->hasMorePages()) disabled @endif">
                @if (! $paginator->hasMorePages())
                    <span class="page-link" aria-hidden="true">
                        <i class="bi bi-chevron-double-right"></i>
                    </span>
                @else
                    <button class="page-link" wire:click="setPage({{ $paginator->lastPage() }})" rel="last" aria-label="Last page">
                        <i class="bi bi-chevron-double-right"></i>
                    </button>
                @endif
            </li>
        </ul>
    </nav>
@endif