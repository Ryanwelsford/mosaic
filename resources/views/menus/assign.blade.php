@extends('layout')
@section('title', $title)

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
                                        <div class="ph-checkbox-label mob-hidden">
                                            @if(isset($menuListings[$product->id]))
                                                {{ "Remove" }}
                                            @else
                                                {{ "Select" }}
                                            @endif
                                        </div>
                                        <label class="ph-checkbox-label">
                                            <input class="ph-checkbox" type="checkbox" name="menuListings[]" value ="{{ $product->id }}" @if(isset($menuListings[$product->id])){{ "checked" }}@endif>
                                            <span class="checkmark"></span>
                                        </label>
                                    </label>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </table>
                @endforeach

                <div class="main-tile-button-container">
                    <button id="previous" class="ph-button ph-button-standard" onclick="">Previous</button>
                    <button id="next" class="ph-button ph-button-standard" onclick="">Next</button>
                </div>
                <div class="main-tile-button-container center-column margin-top">
                    <button form="menuAssign" type="submit" class="ph-button ph-button-important">Submit</button>
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
    function updateSelect() {

        //test = Object.keys(categories)
        let main, updated, current;

        main = document.getElementById("mainSelect");
        updated = document.getElementById("updatedSelect");

        current = main.options[main.selectedIndex].value;

        var selectList = Array(5);
        //these should be alphabetical
        selectList['Frozen'] = ["Toppings", "Cheese", "Starters", "extra"];
        selectList['Dry'] = ["test", "test2"];
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

    function updateTable() {
        //find teh current value of the main select
        let main, current;

        main = document.getElementById("mainSelect");

        current = main.options[main.selectedIndex].value;

        //get all tables
        tables = document.getElementsByClassName("wide-table");
        let toBeOpened;
        //hide all tables
        for (i = 0; i < tables.length; i++) {
            tables[i].style.display = "none";

            //get table required
            if(tables[i].id == current) {
                toBeOpened = tables[i];
            }

        }

        //display required table
        if(toBeOpened !== undefined) {
            toBeOpened.style.display = "table";
        }


    }

    function scrollTable() {
        let main, updated, current;

        //get select elements
        main = document.getElementById("mainSelect");
        updated = document.getElementById("updatedSelect");

        //get concatenated table id
        current = main.options[main.selectedIndex].value+updated.options[updated.selectedIndex].value

        //find table row to be scrolled to
        idToScroll = document.getElementById(current);

        //scroll to table row
        if(idToScroll !== null) {
            console.log(idToScroll);
            idToScroll.scrollIntoView({behavior: 'smooth'});
        }
    }
</script>
<x-top-button></x-top-button>
@endsection
