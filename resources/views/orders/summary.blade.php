@extends('layout')
@section('title', $title)

@section('content')
<div class="grid-container">
    <div class="main-tile tile-all-columns center-column">
        <div class="full-width">
            <h2 class="tile-title tile-all-columns ">Order Summary</h2>
            <div class="grid-2-col-wide">
                <label>Status: {{ $order->status }}</label>
                <label>Store: {{ $store->store_name }} {{ $store->number }}</label>
                <label>Menu: {{ $menu->name }}</label>
                <label>Delivery Date: {{ $order->getDeliveryDate()->format("d M Y") }}</label>
            </div>

            <table class="wide-table full-width reduced-table">
                <th>Id</th>
                <th>Code</th>
                <th>Description</th>
                <th>Unit</th>
                <th>Price</th>
                <th>Quantity</th>



                @foreach($listing as $product)
                <tr>
                    <td>{{$product->id}}</td>
                    <td>{{$product->code}}</td>
                    <td>{{$product->name}}</td>
                    <td>{{$product->units->description}}</td>
                    <td>£{{number_format($product->units->price, 2)}}</td>
                    <td>{{ $product->pivot->quantity }}</td>
                </tr>
                @endforeach
                <tr>
                    <th colspan="4"></th>
                    <th>Total</th>
                    <th>Cases</th>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td>£{{$sum}}</td>
                    <td>{{ $quantity }}</td>
                </tr>
            </table>

            <div class="tile-all-columns center-column margin-top">
                <a href="{{ route('order.print', ["id" => $order->id]) }}" class="ph-button ph-button-standard">@include('icons.print')</a>
            </div>
        </div>
    </div>
</div>
@endsection
