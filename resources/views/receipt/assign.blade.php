@extends('layout')
@section('title', $title)
@section('tools')
<div>
    <button form="form" name="book" type="submit" class="bar-tool-button" value="book"><span class="mobile-hidden">Book</span> <i class="fas fa-book"></i></button>
    <button class="bar-tool-button" onclick="openOrCloseModal('search-modal')" name="save" value="save"><span class="mobile-hidden">Find</span> <i class="fas fa-search-location"></i></button>
</div>
@endsection
@section('content')


<div class="grid-container">
    <div class="main-tile">
        <form method="POST" action="{{ route("receiving.save") }}" id="form" class="center-column">
            <input type="hidden" name="menu_id" value="{{ $menu->id }}">
            <input type="hidden" name="id" value="{{ $receipt->id }}">
            <input type="hidden" name="date" value="{{ $receipt->date }}">
            <input type="hidden" name="reference" value="{{ $receipt->reference }}">
            @csrf

            <div class="full-width">
                <h2 class="tile-title tile-all-columns ">Receipt Details</h2>
                <div class="grid-2-col-wide centered full-width">
                    <label>Store: {{ $store->store_name }}</label>
                    <label>Menu: {{ $menu->name }}</label>
                    <label>Receipt Date: {{ $receipt->date }}</label>
                </div>
                <table class="wide-table full-width reduced-table" id="findable">
                    <th>Id</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Unit</th>
                    <th>Price</th>
                    <th>Quantity</th>

                    @foreach($organisedProducts as $category => $subcategory)
                    <tr>
                        <td id="{{ $category }}"class="span-table-rows" colspan="100%"><h3 class="table-breaker">{{ $category }}</h3></td>
                    </tr>

                        @foreach($subcategory as $key => $products)
                        <tr>
                            <td id="{{ $category }}"class="span-table-rows" colspan="100%"><h5 class="table-breaker">{{ $key }}</h5></td>
                        </tr>

                            @foreach($products as $product)
                            <tr>
                                <td>{{$product->id}}</td>
                                <td>{{$product->code}}</td>
                                <td>{{$product->name}}</td>
                                <td>{{$product->units->description}}</td>
                                <td>£{{number_format($product->units->price,2)}}</td>
                                <td>
                                    @if(isset($product->pivot->quantity))
                                    <input name="product[{{$product->id}}]" class="table-input" type="number" min="0" step ="1"
                                    value="{{$product->pivot->quantity}}">
                                    @else
                                    <input name="product[{{$product->id}}]" value="0" type="number" class="table-input" min="0" step="1" >
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @endforeach
                    @endforeach

                </table>
            </div>
        </form>

        <div class="tile-all-columns center-column margin-top">
                <button form="form" name="book" value="book" type="submit" class="ph-button ph-button-standard ph-button-important">@include("icons.book")</button>
        </div>

    </div>
</div>
<x-tools.find-modal model="products"></x-tools.find-modal>

@endsection
