@extends('layout')
@section('title', $title)
@section('tools')
<button type="submit" form="form" class="bar-tool-button" name="status" value="Booked"> @include("icons.book") </button>
<button type="submit" form="form" class="bar-tool-button" name="status" value="Saved">@include("icons.save")</button>
<button class="bar-tool-button" onclick="openOrCloseModal('search-modal')" name="save" value="save"><span class="mobile-hidden">Find</span> <i class="fas fa-search-location"></i></button>
@endsection
@section('content')
<div class="grid-container">
    <form class="main-tile center-column" id="form" action="{{ route('inventory.new') }}" method="POST">
        <input type="hidden" name="id" value="@if(isset($inventory->id)){{$inventory->id}}@endif">
        @csrf
        <h2 class="tile-title tile-all-columns ">Full Stock Count</h2>
        <div class="grid-2-col-wide centered full-width">
            <label>Store: {{ $store->store_name }}</label>
            <label>Created on: @if(isset($inventory->created_at)){{ $inventory->created_at->format("jS F Y") }}@else {{ $today->format("jS F Y") }}@endif</label>
            <label>Status: @if(isset($inventory->status)){{ $inventory->status }}@else {{ "Active" }}@endif</label>
            <label></label>
        </div>
        <table class="wide-table full-width reduced-table" id="findable">
            <thead>
                <th class="mob-hidden">Code</th>
                <th>Description</th>
                <th class="mob-hidden">Category</th>
                <th>Count</th>
                <th>Total</th>
            </thead>
            <tbody>
                @php
                    $category = $firstCategory;
                @endphp
                <tr>
                    <td class="span-table-rows" colspan="100%"><h3 class="table-breaker">{{ $category }}</h3></td>
                </tr>
                @foreach($products as $product)
                @if($product->category != $category)
                @php
                    $category = $product->category;
                @endphp
                <tr>
                    <td class="span-table-rows" colspan="100%"><h3 class="table-breaker">{{ $category }}</h3></td>
                </tr>
                @endif
                    <tr>
                        <td class="mob-hidden">{{ $product->code }}</td>
                        <td>{{ $product->name }}</td>
                        <td class="mob-hidden">{{ $product->subcategory }}</td>
                        <td>
                            <div class="counter-holder">
                                <input  data-count="{{ $product->units->quantity }}" type="number" class="table-input count-box" min="0" step="1" placeholder="Case: {{ $product->units->description }}">

                                @if($product->units->pack != "none")
                                    <input data-count="{{ $product->units->packQuantity() }}" type="number" class="table-input count-box" min="0" step="1" placeholder="Pack: {{ $product->units->pack_description }}">
                                @endif

                                <input id="box" data-count="1" type="number" class="table-input count-box" min="0" step="1" placeholder="Each: 1">
                            </div>
                        </td>
                        <td class="total-td">
                            @if(isset($mappedProducts[$product->id]))
                                <input name="product[{{$product->id}}]" type="number" class="table-input total-box" min="0" step="1" value="{{$mappedProducts[$product->id]}}">
                            @else
                                <input name="product[{{$product->id}}]" type="number" class="table-input total-box" min="0" step="1" value="0">
                            @endif
                        </td >
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="tile-all-columns center-column margin-top">
            <button form="form" name="status" value="Booked" type="submit" class="ph-button ph-button-standard ph-button-important">@include("icons.book")</button>
        </div>
    </form>
</div>

<x-tools.find-modal model="products"></x-tools.find-modal>

<script>

</script>
@endsection
