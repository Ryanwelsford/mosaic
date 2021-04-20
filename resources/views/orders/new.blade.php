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
        <div class="main-tile-button-container center-column margin-top">
            <button form="form_product" type="submit" class="ph-button ph-button-important">Submit</button>
        </div>
    </div>
</div>

<script>

</script>
@endsection
