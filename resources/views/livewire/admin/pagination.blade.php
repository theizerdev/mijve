@if ($paginator->hasPages())
  <nav class="d-flex justify-content-between align-items-center">

    <div>
      <ul class="pagination mb-0">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
          <li class="page-item disabled">
            <span class="page-link">
              <i class="ri ri-arrow-left-s-line"></i>
            </span>
          </li>
        @else
          <li class="page-item">
            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
              <i class="ri ri-arrow-left-s-line"></i>
            </a>
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
                  <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
              @endif
            @endforeach
          @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
          <li class="page-item">
            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
              <i class="ri ri-arrow-right-s-line"></i>
            </a>
          </li>
        @else
          <li class="page-item disabled">
            <span class="page-link">
              <i class="ri ri-arrow-right-s-line"></i>
            </span>
          </li>
        @endif
      </ul>
    </div>
  </nav>
@endif
