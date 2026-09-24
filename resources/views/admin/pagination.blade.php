@if ($paginator->hasPages())
    <nav class="pager">
        @if ($paginator->onFirstPage())
            <span></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}">← Anteriores</a>
        @endif
        <span class="muted small">Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}</span>
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}">Siguientes →</a>
        @else
            <span></span>
        @endif
    </nav>
@endif
