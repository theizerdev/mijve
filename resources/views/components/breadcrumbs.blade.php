<nav aria-label="breadcrumb">
  <ol class="breadcrumb breadcrumb-style">
    @foreach($breadcrumbs as $breadcrumb)
      @if($breadcrumb['active'])
        <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['title'] }}</li>
      @else
        <li class="breadcrumb-item">
          <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
        </li>
      @endif
    @endforeach
  </ol>
</nav>

<style>
  .breadcrumb-style {
    background-color: transparent;
    padding: 0.75rem 1rem;
    margin-bottom: 1rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
  }
  
  .breadcrumb-style .breadcrumb-item + .breadcrumb-item::before {
    content: ">";
    color: #6c757d;
  }
  
  .breadcrumb-style .breadcrumb-item a {
    color: #007bff;
    text-decoration: none;
  }
  
  .breadcrumb-style .breadcrumb-item a:hover {
    text-decoration: underline;
  }
  
  .breadcrumb-style .breadcrumb-item.active {
    color: #495057;
  }
</style>