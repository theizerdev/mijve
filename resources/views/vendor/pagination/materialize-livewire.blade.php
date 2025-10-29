@if ($paginator->hasPages())
  <nav class="d-flex justify-content-between align-items-center">
    <div>
      <p class="mb-0 text-muted">
        Mostrando {{ $paginator->firstItem() }} a {{ $paginator->lastItem() }} de {{ $paginator->total() }} resultados
      </p>
    </div>

    <div>
      <ul class="pagination mb-0">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
          <li class="page-item disabled">
            <span class="page-link">
              <i class="ti ti-chevron-left"></i>
            </span>
          </li>
        @else
          <li class="page-item">
            <button type="button" class="page-link" wire:click="previousPage" wire:loading.attr="disabled" rel="prev">
              <i class="ti ti-chevron-left"></i>
            </button>
          </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
          {{-- "Three Dots" Separator --}}
          @if (is_string($element))
            <li class="page-item disabled">
              <span class="page-link">{{ $element }}</span>
            </li>
          @endif

          {{-- Array Of Links --}}
          @if (is_array($element))
            @foreach ($element as $page => $url)
              @if ($page == $paginator->currentPage())
                <li class="page-item active">
                  <span class="page-link">{{ $page }}</span>
                </li>
              @else
                <li class="page-item">
                  <button type="button" class="page-link" wire:click="gotoPage({{ $page }})">{{ $page }}</button>
                </li>
              @endif
            @endforeach
          @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
          <li class="page-item">
            <button type="button" class="page-link" wire:click="nextPage" wire:loading.attr="disabled" rel="next">
              <i class="ti ti-chevron-right"></i>
            </button>
          </li>
        @else
          <li class="page-item disabled">
            <span class="page-link">
              <i class="ti ti-chevron-right"></i>
            </span>
          </li>
        @endif
      </ul>
    </div>
  </nav>
@endif