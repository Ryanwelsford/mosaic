@extends('layout')
@section('title', $title)

@section("tools")
<button form="menuAssign" type="submit" class="bar-tool-button">@include("icons.save")</button>
@endsection

@section('content')
<div class="grid-container">
    <div class="main-tile tile-all-columns center-column">
        <label class="select-label">Select a Category</label>
        <select id="mainSelect" class="main-select main-select-large" placeholder="Select from the list">
            @foreach($categories as $key=>$category)
            <option value= "{{ $key }}">{{ $key }}</option>
            @endforeach
        </select>
        <select id="updatedSelect" class="main-select main-select-large" placeholder="Select from the list">
            @foreach($categories['Chilled'] as $option)
                <option value= "{{ $option }}">{{ $option }}</option>
            @endforeach
        </select>
    </div>

    <form id="menuAssign" action="{{ route("menu.assign") }}" method="POST" class="main-tile tile-all-columns center-column">
        @csrf
        <input type="hidden" name="menu_id" value="{{ $menu->id }}">
        <h2>Select Products</h2>


            <div class="grid-2-col-wide">
                <div>Menu Name: {{ $menu->name }}</div>
                <div>Created at: {{ $menu->created_at->format('d/m/Y') }}</div>
            </div>

                @foreach($organisedProducts as $category => $subcategory)

                @if($category == $defaultOpenTab)
                <table class="wide-table" id="{{ $category }}">
                @else
                <table class="wide-table hidden-tab" id="{{ $category }}">
                @endif

                <th>
                    <p class="mob-hidden">Product Name</p>
                    <p class="mobile-only">Details</p>
                </th>
                <th class="mob-hidden">
                    Supplier Code
                </th>
                <th class="mob-hidden">
                    Category
                </th>
                <th class="mob-hidden">
                    Subcategory
                </th>
                <th>
                    In Menu
                </th>
                    @foreach($subcategory as $key => $products)
                    <tr>
                        <td id="{{ $category.$key }}"class="span-table-rows" colspan="100%"><h3>{{ $key }}</h3></td>
                    </tr>
                        @foreach ($products as $product)
                            <tr>
                                <td>
                                    {{ $product->name }}
                                    <p class='mobile-only'>{{ $product->code }}</p>
                                </td>

                                <td class="mob-hidden">
                                    {{ $product->code }}
                                </td>

                                <td class="mob-hidden">
                                    {{ $product->category }}
                                </td>

                                <td class="mob-hidden">
                                    {{ $product->subcategory }}
                                </td>

                                <td class=>
                                    <label class="table-button-holder">
                                        <div class="ph-checkbox-label mob-hidden" onclick="updateDivText(this)">
                                            @if(isset($menuListings[$product->id]))
                                                {{ "Remove" }}
                                            @else
                                                {{ "Select" }}
                                            @endif
                                        </div>
                                        <label class="ph-checkbox-label">
                                            <input class="ph-checkbox" type="checkbox" name="menuListings[]" value ="{{ $product->id }}" @if(isset($menuListings[$product->id])){{ "checked" }}@endif>
                                            <span onclick="updateText(this)"class="checkmark"></span>
                                        </label>
                                    </label>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </table>
                @endforeach

                <div class="main-tile-button-container">
                    <button id="previous" class="ph-button ph-button-standard" type="button" onclick="previousCategory()">Previous</button>
                    <button id="next" class="ph-button ph-button-standard" type="button" onclick="nextCategory()">Next</button>
                </div>
                <div class="main-tile-button-container center-column margin-top">
                    <button form="menuAssign" type="submit" class="ph-button ph-button-important">@include("icons.save")</button>
                </div>

    </form>
</div>

<script>
    let main = document.getElementById("mainSelect");
    main.onchange = function(event) {
        updateSelect();
        updateTable();
    }

    let subSelect = document.getElementById("updatedSelect");
    subSelect.onchange = function(event) {
        scrollTable();
    }

</script>
@endsection
