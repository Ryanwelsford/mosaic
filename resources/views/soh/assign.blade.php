@extends('layout')
@section('title', $title)
@section('tools')
<button class="bar-tool-button" onclick="openOrCloseModal('search-modal')" name="save" value="save"><span class="mobile-hidden">Find</span> <i class="fas fa-search-location"></i></button>
<button form="form" class="bar-tool-button" type="submit" name="save" value="save">@include('icons.save')</button>
@endsection


@section('content')
<div class="grid-container">
    <div class="main-tile">
        <form id="form" action="{{ route('soh.assign') }}" method="POST" class="full-width center-column">
            @csrf
            <h2 class="tile-title tile-all-columns ">Select Products for Stock On Hand</h2>
            @error('sohList')
                <div class="confirmation-banner confirmation-message margin-bottom-2 full-width error-banner">
                    <h3>Error: {{ $message }} <button onclick="closeDiv(event)" class="close-X">X</button></h3>
                </div>
            @enderror
            <table class="wide-table full-width reduced-table" id="findable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Category</th>
                        <th>Subcategory</th>
                        <th>Options</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($productList as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->code }}</td>
                        <td>{{ $product->category }}</td>
                        <td>{{ $product->subcategory }}</td>
                        <td>
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
                </tbody>
            </table>

            <div class="tile-all-columns center-column margin-top">
                <button form="form" name="save" value="save" type="submit" class="ph-button ph-button-standard ph-button-important">@include('icons.save')</button>
        </div>
        </form>
    </div>
</div>

<x-tools.find-modal model="products"></x-tools.find-modal>
@endsection
