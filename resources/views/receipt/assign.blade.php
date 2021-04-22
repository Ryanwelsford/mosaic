@extends('layout')
@section('title', $title)
@section('tools')
<div>
    <button form="form" name="book" type="submit" class="bar-tool-button" value="book"><span class="mobile-hidden">Book</span> <i class="fas fa-book"></i></button>
    <button class="bar-tool-button" onclick="searchModal()" name="save" value="save"><span class="mobile-hidden">Find</span> <i class="fas fa-search-location"></i></button>
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
                    <label>Store: {{ $store->name }}</label>
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
                                    <input name="product[{{$product->id}}]" class="table-input" type="number" min="0" step ="1"
                                    value=@if(isset($product->pivot)){{$product->pivot->quantity}}@else{{$origin}}@endif

                                    >
                                </td>
                            </tr>
                            @endforeach
                        @endforeach
                    @endforeach

                </table>
            </div>
        </form>

        <div class="tile-all-columns center-column margin-top">
                <button form="form" name="book" value="book" type="submit" class="ph-button ph-button-standard ph-button-important">Book Receipt <i class="fas fa-book"></i></button>
        </div>

    </div>
</div>

<section class="modal" id="search-modal">
    <div class="modal-internal small-modal">
        <div class="modal-title">Find products on screen <button onclick="searchModal()" class="close-X">X</button></div>
        <div class="modal-content vert-center">
            <div class="modal-center">
                <form class="search-form grid-2-col-wide centered" method="GET" action="{{ route("order.view") }}">
                    <label>Search Products</label>
                    <input name="search" type ="text" id="search-bar" placeholder="Enter Product to Find">
                    <div class="tile-all-columns">
                        <button type="button" onclick="findInTable(search, findable)" class="ph-button ph-button-standard full-width">Find</button>
                    </div>
                    <label id="response" class="tile-all-columns"></label>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
