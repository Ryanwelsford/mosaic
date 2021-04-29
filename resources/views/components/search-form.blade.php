<form class="search-form grid-2-col-wide table-width-match" method="GET" action="{{ $action }}">
    <label>Search {{ $model }}</label>
    <div class="search-with-button">
        <input name="search" type ="text" class=""  id="search-bar" placeholder="Search here" value="@if(isset($search)){{$search}}@endif">
        <button type ="submit" class="ph-button ph-button-standard">Search</button>
    </div>
</form>
