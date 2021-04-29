@extends('layout')
@section('title', $title)
@section('tools')
<button form="form" class="bar-tool-button" type="submit" name="save" value="save">@include('icons.save')</button>
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

    <form id="form" action="{{ route('soh.assign') }}" method="POST" class="main-tile tile-all-columns">
        @csrf
            <h2 class="tile-title tile-all-columns ">Select Products for Stock On Hand</h2>
            @error('sohList')
                <div class="confirmation-banner confirmation-message margin-bottom-2 full-width error-banner">
                    <h3>Error: {{ $message }} <button onclick="closeDiv(event)" class="close-X">X</button></h3>
                </div>
            @enderror
        @foreach($organisedProducts as $category => $subcategory)

                @if($category == $defaultOpenTab)
                <table class="wide-table full-width reduced-table" id="{{ $category }}">
                @else
                <table class="wide-table hidden-tab full-width reduced-table" id="{{ $category }}">
                @endif

                <th>
                    Name
                </th>
                <th class="mob-hidden">
                    Supplier Code
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
                                    {{ $product->subcategory }}
                                </td>

                                <td class=>
                                    <label class="table-button-holder">
                                        <div class="ph-checkbox-label mob-hidden" onclick="updateDivText(this)">
                                            @if(isset($assignedMap[$product->id]))
                                                {{ "Remove" }}
                                            @else
                                                {{ "Select" }}
                                            @endif
                                        </div>
                                        <label class="ph-checkbox-label">
                                            <input class="ph-checkbox" type="checkbox" name="sohList[]" value ="{{ $product->id }}" @if(isset($assignedMap[$product->id])){{ "checked" }}@endif>
                                            <span onclick="updateText(this)"class="checkmark"></span>
                                        </label>
                                    </label>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </table>
                @endforeach

                <div class="main-tile-button-container full-width">
                    <button id="previous" class="ph-button ph-button-standard" type="button" onclick="previousCategory()">Previous</button>
                    <button id="next" class="ph-button ph-button-standard" type="button" onclick="nextCategory()">Next</button>
                </div>
                <div class="tile-all-columns center-column margin-top">
                    <button form="form" name="save" value="save" type="submit" class="ph-button ph-button-standard ph-button-important">@include('icons.save')</button>
                </div>
    </div>
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
<x-tools.find-modal model="products"></x-tools.find-modal>
@endsection
