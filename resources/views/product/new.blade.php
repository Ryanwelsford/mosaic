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
                    <input name="name" class="@error('name') input-error @enderror" type="text" value="@if(isset($product->name)){{ $product->name }} @endif">
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

                <!--form needs to be updated to only have Case, then each, then pack set the select to a type text and mark as readonly-->
                <form method="POST" action="{{ route("product.new") }}">
                    <div class="center-column" id="target">
                        <div class="grid-2-col-wide">
                            <label>Case Details:</label>
                            <label>Case</label>

                            <label>Description: @error('case.description')<span class="error-text">*</span>@enderror</label>

                            <div>
                                <input class="@error('case.description') input-error @enderror" name="case[description]"type="text" value="@if(isset($case['description'])){{$case['description']}}@endif">
                                @error('case.description')
                                    <div class="small-error-text error-text">{{ $message }} </div>
                                @enderror
                            </div>

                            <label>Case Price: @error('case.price')<span class="error-text">*</span>@enderror</label>

                            <div>
                                <input name="case[price]"type="number" step="0.01" min="0.00" value="@if(isset($case['price'])){{$case['price']}}@endif">
                                @error('case.price')
                                    <div class="small-error-text error-text">{{ $message }} </div>
                                @enderror
                            </div>

                            <label>Quanitity per Case: @error('case.quantity')<span class="error-text">*</span>@enderror</label>
                            <div>
                                <input name="case[quantity]"type="number" step="1" min="0" value="@if(isset($case['quantity'])){{$case['quantity']}}@endif">
                                @error('case.quantity')
                                    <div class="small-error-text error-text">{{ $message }} </div>
                                @enderror
                            </div>

                            <div class="form-note tile-all-columns">Enter the number of items per case</div>
                            <label class="margin-top-2">Pack Details:</label>

                            <select name="pack[details]" id="packSelect"class=" margin-top-2" onchange="revealUnits()">
                                <option @if(isset($pack['details']) && $pack['details'] == "none" ){{ "selected" }} @endif value="none">N/A</option>
                                <option @if(isset($pack['details']) && $pack['details'] == "Pack" ){{ "selected" }} @endif value="Pack">Required</option>
                            </select>
                            <div class="form-note tile-all-columns">If no pack information is required leave as N/A</div>

                            <div id="packTab" class="tile-all-columns @if((isset($pack['details']) && $pack['details'] == "none") || !isset($pack['details'])){{ "hidden" }} @endif">
                                <div class="grid-2-col-wide full-width">
                                    <label>Pack Description: @error('unit["description"]') {{  "*" }} @enderror</label>
                                    <input name="pack[description]"type="text" value="@if(isset($pack['description'])){{$pack['description']}}@endif">
                                    <label>Packs per Case:</label>
                                    <input name="pack[quantity]" type="number" step="1" min="0"
                                    value="@if(isset($pack['quantity'])){{intval($pack['quantity'])}}@endif">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </form>

        <div class="main-tile-button-container">
            <button id="previous" class="ph-button ph-button-standard" onclick="openTab(-1)">@include('icons.previous')</button>
            <button id="next" class="ph-button ph-button-standard" onclick="openTab(1)">@include('icons.next')</button>
        </div>
        <div class="main-tile-button-container center-column margin-top">
            <button form="form_product" type="submit" class="ph-button ph-button-important">@include('icons.save')</button>
        </div>
    </div>
</div>

<script>

    function revealUnits() {
        let select = document.getElementById("packSelect");
        let div = document.getElementById("packTab");

        let current = select.options[select.selectedIndex].value;

        if(current == "none") {
            div.classList.add("hidden");
        }
        else {
            div.classList.remove("hidden")
        }
    }
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
