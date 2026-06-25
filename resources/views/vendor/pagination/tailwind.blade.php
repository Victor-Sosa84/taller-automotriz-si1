@if ($paginator->hasPages())
<nav style="display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; margin-top:1rem;">
    <div style="color:var(--muted); font-size:.82rem;">
        Mostrando <strong>{{ $paginator->firstItem() }}</strong>–<strong>{{ $paginator->lastItem() }}</strong>
        de <strong>{{ $paginator->total() }}</strong> resultado{{ $paginator->total() === 1 ? '' : 's' }}
    </div>

    <div style="display:flex; align-items:center; gap:.4rem;">
        {{-- Anterior --}}
        @if ($paginator->onFirstPage())
            <span class="btn btn-ghost btn-sm" style="opacity:.4; cursor:not-allowed;" aria-disabled="true">‹ Anterior</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-ghost btn-sm">‹ Anterior</a>
        @endif

        {{-- Números de página --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span style="color:var(--muted); padding:0 .3rem;">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="btn btn-primary btn-sm" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="btn btn-ghost btn-sm">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Siguiente --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-ghost btn-sm">Siguiente ›</a>
        @else
            <span class="btn btn-ghost btn-sm" style="opacity:.4; cursor:not-allowed;" aria-disabled="true">Siguiente ›</span>
        @endif
    </div>
</nav>
@endif