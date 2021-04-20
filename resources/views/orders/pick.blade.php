@extends('layout')
@section('title', $title)
@section('tools')
<div>
    <button form="form_order" name="book" type="submit" class="bar-tool-button" value="book"><span class="mobile-hidden">Book</span> <i class="fas fa-book"></i></button>
    <button form="form_order" class="bar-tool-button" name="save" value="save"><span class="mobile-hidden">Save</span> <i class="far fa-save"></i></i></button>
</div>
@endsection
@section('content')


<div class="grid-container">
    <div class="main-tile">
        <form method="POST" action="{{ route("order.save") }}" id="form_order" class="center-column">
            @csrf
            <div class="full-width">
                <h2 class="tile-title tile-all-columns ">Order Details</h2>
                <div class="grid-2-col-wide centered full-width">
                    <label>Status: Saved</label>
                    <label>Store: Northampton 3</label>
                    <label>Menu: Updated Menu</label>
                    <label>Creation Date: Today</label>
                </div>
                <table class="wide-table full-width reduced-table">
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
                                <td>£{{$product->units->price}}</td>
                                <td>
                                    <input name="product[{{$product->id}}]" class="table-input" type="number" min="0" step ="1" value="0">
                                </td>
                            </tr>
                            @endforeach
                        @endforeach
                    @endforeach

                </table>
            </div>
        </form>

        <div class="tile-all-columns center-column margin-top">
                <button form="form_order" name="book" value="book" type="submit" class="ph-button ph-button-standard ph-button-important">Book Order <i class="fas fa-book"></i></button>
        </div>

    </div>
</div>
@endsection
