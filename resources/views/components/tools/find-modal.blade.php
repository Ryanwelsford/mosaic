@extends("modals.layout")
@section("modal_title" , "Find ". $model ." on screen")


@section("modal_content")
<div class="search-form grid-2-col-wide centered">
    <label>Search {{ ucwords($model) }}</label>
    <input name="search" type ="text" id="search-bar" placeholder="Enter Product to Find">
    <div class="tile-all-columns">
        <button type="button" onclick="findInTable('search-bar', 'findable')" class="ph-button ph-button-standard full-width">Find</button>
    </div>
    <label id="response" class="tile-all-columns"></label>
</div>
@endsection
