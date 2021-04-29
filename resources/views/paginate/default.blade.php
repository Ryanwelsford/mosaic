<p>Displaying {{ ($paginator->currentPage()-1)*$paginator->perPage()+1 }} to {{(($paginator->currentPage()-1)*($paginator->perPage()))+$paginator->count()}} of {{ $paginator->total() }}</p>

@if ($paginator->lastPage() > 1)
<ul class="pagination">
    @php
    //attach sort and search options when required
        $content = '';

        if(isset($search)) {
            $content .= "&search=".$search;
        }

        if(isset($sort)) {
            $content .= "&sort=".$sort;
        }
    @endphp
    <li class="left">
        @if($paginator->currentPage() != 1)
        <a class="ph-button ph-button-standard ph-button-small" href="{{ $paginator->url($paginator->currentPage()-1) }}{{ $content }}">@include('icons.previous')</a>
        @endif
    </li>
    <li class="right">
        @if($paginator->currentPage() != $paginator->lastPage())
        <a class="ph-button ph-button-standard ph-button-small" href="{{ $paginator->url($paginator->currentPage()+1) }}{{ $content }}">@include('icons.next')</a>
        @endif
    </li>
</ul>
@endif
