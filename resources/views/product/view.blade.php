@extends('layout')
@section('title', $title)

@section('tools')
    <button class="bar-tool-button" onclick="searchModal()"><span class="mobile-hidden">Search</span> <i class="fas fa-search"></i></button>
@endsection

@section('content')
<div class="grid-container">
    <div class="main-tile tile-all-columns center-column">
        <form class="search-form grid-2-col-wide table-width-match" method="GET" action="{{ route("product.view") }}">
            <label>Search Products  </label>
            <div class="search-with-button">
                <input name="search" type ="text" class=" "  id="search-bar" placeholder="Search here" value="@if(isset($search)){{$search}}@endif">
                <button type ="submit" class="ph-button ph-button-standard">Search</button>
            </div>
        </form>
    </div>
    <div class="main-tile tile-all-columns center-column">
        <h2>Product Details</h2>
        @if(session("confirmation"))
        <div class ="confirmation-banner confirmation-message table-fit margin-bottom-2">
            <h3>{{ session("confirmation") }} <button onclick="closeDiv(event)" class="close-X">X</button></h3>
        </div>
        @endif

        @if($products->count() < 1 && !isset($search))
            <p>No menus currently exist, create a new menu <a href="{{ route("product.new") }}">here</a></p>
        @endif

        @if(isset($search))
            <p>Displaying {{$products->count()}} results for... <span class="italics">{{ $search }}</span> @if(isset($sort)) {{ "sorted by ".$sort }} @endif</p>
        @endif


        <table class="wide-table">
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
                Options
            </th>

            @foreach($products as $product)
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

                    <td>
                        <div class="table-button-holder">
                            <a href="{{ route('product.new', ['id' => $product->id]) }}"class="ph-button ph-button-standard table-button">Edit<img src="/images/icons/edit-48-black.png"></a>
                            <form method="POST" action="{{ route("product.destroy", $product) }}">
                                <button class="ph-button ph-button-important table-button" type="submit">Delete</button>
                                @csrf
                                @method('delete')
                            </form>
                        </div>

                    </td>
                </tr>

            @endforeach
        </table>
    </div>

    <section class="modal" id="search-modal">
        <div class="modal-internal small-modal">
            <div class="modal-title">Search Menus <button onclick="searchModal()" class="close-X">X</button></div>
            <div class="modal-content vert-center">
                <div class="modal-center">
                    <form class="search-form grid-2-col-wide centered" method="GET" action="{{ route("product.view") }}">
                        <label>Search Menus</label>
                        <input name="search" value="@if(isset($search)){{$search}}@endif" type ="text" class=" "  id="search-bar" placeholder="Search here">
                        <label>Sort by</label>
                        <select name="sort">
                            @foreach($searchFields as $field)
                                <option value="{{ $field }}">{{str_replace("_", " ", $field)}}</option>
                            @endforeach
                        </select>
                        <input type="submit" class="ph-button ph-button-standard tile-all-columns">
                    </form>
                </div>
            </div>
        </div>
    </section>

</div>
<x-top-button></x-top-button>
<script>

    function redrawTable() {
        removeTag("tr", 1);
        addTableTr();
    }

    function removeTag(tag, countToLeave = 1) {
        //get all elements by their tag
        elements = document.getElementsByTagName(tag);

        //remove all elements from list end, leaving count behind.
        if(elements.length > countToLeave) {
            for(i = elements.length ; elements.length > countToLeave; i--) {
                elements[i-1].remove();
            }
        }
    }

    function addTableTr() {

    }

    function updateSelect() {
        //let categories = JSON.parse();

        //test = Object.keys(categories)
        let main, updated, current;

        main = document.getElementById("mainSelect");
        updated = document.getElementById("updatedSelect");

        current = main.options[main.selectedIndex].value;

        var selectList = Array(5);
        //these should be alphabetical
        console.log(selectList);
        if(current in selectList) {
            //remove all current options.
            while(updated.options.length > 0) {
            updated.remove(0);
            }

        //create new options based on list
            for(let o = 0; o <selectList[current].length; o++) {
                newOption = document.createElement("option");
                newOption.value = selectList[current][o];
                newOption.text = selectList[current][o];

                updated.appendChild(newOption);
            }
        }


    }
</script>
@endsection
