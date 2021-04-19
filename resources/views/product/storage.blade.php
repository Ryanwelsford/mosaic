@extends('layout')
@section('title', $title)

@section('content')
<div class="center-column">
    <div class="main-tile tile-all-columns center-column mono-tile">
        <form method="POST" action="{{ route("product.new") }}" id="form_product" class="center-column">

            <div class="grid-2-col-wide active-tab tab">
                @csrf

                <h2 class="tile-title tile-all-columns">Product Details</h2>
                <input name="id" type="hidden" value="@if(isset($product->id)) {{ $product->id }} @endif">
                <label>Name: @error('name') <span class="error-text">*</span> @enderror</label>
                <div>
                    <input name="name" class="@error('name') input-error @enderror"type="text" value="@if(isset($product->name)){{ $product->name }} @endif">
                    @error('name')
                        <div class="small-error-text error-text">{{ $message }} </div>
                    @enderror
                </div>

                <label>Supplier Code: @error('code') <span class="error-text">*</span> @enderror</label>
                <div>
                    <input name="code" class="@error('code') input-error @enderror" type="number" value="@if(isset($product->code)){{$product->code}}@endif">
                    @error('code')
                        <div class="small-error-text error-text">{{ $message }} </div>
                    @enderror
                </div>
                <label>Category:</label>
                <select name="category" id="mainSelect">
                    @foreach($categories as $key => $category)
                        <option @if(isset($product->category) && $product->category == $key) {{ "selected" }} @endif value="{{ $key }}">{{ $key }}</option>
                    @endforeach
                </select>
                <label >Sub-Category:</label>
                <select name="subcategory" id="updatedSelect">
                    @if(isset($product->category))
                        @foreach($categories[$product->category] as $key => $value)
                            <option @if($product->subcategory == $value) {{ "selected" }} @endif value="{{ $value }}">{{ $value }}</option>
                        @endforeach
                    @else
                        @foreach($categories["Chilled"] as $key => $value)
                            <option value="{{ $value }}">{{ $value }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="center-column hidden-tab tab">

                <h2 class="tile-title tile-all-columns">Product Units</h2>
                <div class="main-tile-button-container" id="duplicateHolder"><a id="duplicate" onclick="duplicate('dupe', 'target', ['dupe', 3])" class="ph-button ph-button-standard ph-button-rounded">+</a><a id="duplicate" onclick="removeDupe('dupe')" class="ph-button ph-button-standard ph-button-rounded">-</a></div>

                <!--form needs to be updated to only have Case, then each, then pack set the select to a type text and mark as readonly-->
                <form method="POST" action="{{ route("product.new") }}">
                    <div class="center-column" id="target">
                        <div id="dupe" class="dupe grid-2-col-wide">
                            <label>Unit Type:</label>
                            <select name="unit[type][]">
                                <option>Case</option>
                                <option>Pack</option>
                                <option>Each</option>
                            </select>
                            <label>Description: @error('unit["description"]') {{  "*" }} @enderror</label>
                            <input  name="unit[description][]"type="text" value="">
                            <label>Case Price:</label>
                            <input name="unit[price][]"type="number" step="0.01">
                        </div>
                    </div>
                </form>
            </div>

        </form>

        <div class="main-tile-button-container">
            <button id="previous" class="ph-button ph-button-standard" onclick="openTab(-1)">Previous</button>
            <button id="next" class="ph-button ph-button-standard" onclick="openTab(1)">Next</button>
        </div>
        <div class="main-tile-button-container center-column margin-top">
            <button form="form_product" type="submit" class="ph-button ph-button-important">Submit</button>
        </div>
    </div>
</div>

<script>
    function openTab(which) {
        let openTab, tabs, tracker, buttonP, buttonN;

        //gather objects on page
        openTab = document.getElementsByClassName("active-tab")[0];
        tabs = document.getElementsByClassName("tab");
        tracker = 0;
        buttonP = document.getElementById("previous");
        buttonN = document.getElementById("next");

        //find which tab is currently the open tab
        for(i = 0; i< tabs.length; i++) {

            if(openTab === tabs[i]) {
                tracker = i;
            }
        }

        //i.e. try to hit previous while first tab is open
        if (which == -1 && tracker == 0) {
            buttonP.disabled = true;
        }
        //if at end of tabs
        else if (which == 1 && tracker == tabs.length-1) {
            buttonN.disabled = true;
        }
        //otherwise change tab in given direction
        else {
            tabs[tracker+which].className = tabs[tracker+which].className.replace("hidden-tab", "active-tab");
            openTab.className = openTab.className.replace("active-tab", "hidden-tab");
            buttonN.disabled = false;
            buttonP.disabled = false;
        }

    }


    //replace this function with one that creates a new item with each/pack as the select option
    function duplicate(dupeTarget, parent, max) {

        let maximumDupes = max[1];
        let dupeClass = max[0];
        console.log(parent);

        let currentDupes = document.getElementsByClassName(dupeClass);

        //check if a maximum amount of duplicate nodes has been reached
        if (currentDupes.length < maximumDupes) {
            let parentNode = document.getElementById('target');

            let dupe = document.getElementById(dupeTarget);
            console.log(dupe);
            let clone = dupe.cloneNode(true);
            console.log(parentNode);
            parentNode.append(clone);
        }

    }

    function removeDupe(nodes) {
        let nodesList = document.getElementsByClassName(nodes);
        //dont allow all elements to be removed, min 1
        if(nodesList.length > 1) {
            //remove last element in list
            nodesList[nodesList.length-1].remove();
        }
    }

    let main = document.getElementById("mainSelect");
    main.onchange = function(event) {
        updateSelect();
    }

</script>
@endsection
