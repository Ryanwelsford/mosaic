@extends("modals.layout")
@section("modal_title" , "Add Products To Waste")


@section("modal_content")
<form class="center-column">
    <input type="hidden" id="_token" value="{{ csrf_token() }}">
    <div class="center-column">
        <label class="select-label">Select a Category</label>
        <select onchange="loadCategories(event)" class="main-select main-select-large" placeholder="Select from the list">
            @foreach($categories as $key=>$category)
            <option value= "{{ $key }}">{{ $key }}</option>
            @endforeach
        </select>
    </div>

    <div id="productContainer" class="vert-scroll ">
    </div>
    <div id="error" class="error-text small-error-text"></div>
</form>


@endsection
