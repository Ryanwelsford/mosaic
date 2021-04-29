@extends("modals.layout")
@section("modal_title" , "Search")


@section("modal_content")
<form class="search-form grid-2-col-wide centered" method="GET" action="{{ route($action) }}">
    <label>Search {{ $model }}</label>
    <input name="search" value="@if(isset($search)){{$search}}@endif" type ="text" class=" "  id="search-bar" placeholder="Search here">
    <label>Sort by</label>
    <select name="sort">
        @foreach($fields as $field)
            <option value="{{ $field }}">{{ucwords(str_replace("_", " ", $field))}}</option>
        @endforeach
    </select>
    <input type="submit" class="ph-button ph-button-standard tile-all-columns">
</form>
@endsection
