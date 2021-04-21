@extends('layout')
@section('title', $title)

@section("tools")
    <button class="bar-tool-button" onclick="searchModal()"><span class="mobile-hidden">Search</span> <i class="fas fa-search"></i></button>
@endsection

@section('content')
<div class="grid-container">
    <div class="main-tile tile-all-columns center-column">
        <h2>Order Details</h2>
        <form class="search-form grid-2-col-wide table-width-match" method="GET" action="{{ route("order.view") }}">
            <label>Search Orders</label>
            <div class="search-with-button">
                <input name="search" type ="text" class=""  id="search-bar" placeholder="Search here" value="@if(isset($search)){{$search}}@endif">
                <button type ="submit" class="ph-button ph-button-standard">Search</button>
            </div>
        </form>

        @if($orders->count() < 1 && !isset($search))
            <p>No orders currently exist, create a new order <a href="{{ route("order.new") }}">here</a></p>
        @endif

        @if(isset($search))
            <p>Displaying {{$orders->count()}} results for... <span class="italics">{{ $search }}</span> @if(isset($sort)) {{ "sorted by ".$sort }} @endif</p>
        @endif

        @if($orders->count() >= 1)
        <table class="wide-table full-width reduced-table">
            <th>
                <p class="">Order #</p>
            </th>
            <th class="">
                Reference
            </th>
            <th class="">
                Status
            </th>
            <th class="">
                Created on
            </th>
            <th class="">
                Delivery
            </th>
            <th>
                Options
            </th>

            @foreach($orders as $order)
                <tr>
                    <td>
                        {{ $order->id }}
                    </td>

                    <td class="">
                        {{ $order->reference }}
                    </td>

                    <td class="">
                        {{ $order->status }}
                    </td>

                    <td class="">
                        {{ $order->created_at->format('d M Y') }}
                    </td>

                    <td class="">
                        {{ $order->getDeliveryDate()->format('d M Y') }}
                    </td>

                    <td>
                        <div class="table-button-holder">
                            @if($order->status == "Booked")
                                <a href="{{ route('order.summary', ['id' => $order->id]) }}"class="ph-button ph-button-standard table-button">Summary<img src="/images/icons/edit-48-black.png"></a>
                            @else
                                <a href="{{ route('order.new', ['id' => $order->id]) }}"class="ph-button ph-button-standard table-button">Edit<img src="/images/icons/edit-48-black.png"></a>

                                <form method="POST" action="{{ route("order.destroy", $order) }}">
                                    <button class="ph-button ph-button-important table-button" type="submit">Delete</button>
                                    @csrf
                                    @method('delete')
                                </form>
                            @endif


                        </div>

                    </td>
                </tr>

            @endforeach
        </table>
        @endif
    </div>

    <section class="modal" id="search-modal">
        <div class="modal-internal small-modal">
            <div class="modal-title">Search Menus <button onclick="searchModal()" class="close-X">X</button></div>
            <div class="modal-content vert-center">
                <div class="modal-center">
                    <form class="search-form grid-2-col-wide centered" method="GET" action="{{ route("order.view") }}">
                        <label>Search Menus</label>
                        <input name="search" value="@if(isset($search)){{$search}}@endif" type ="text" class=" "  id="search-bar" placeholder="Search here">
                        <label>Sort by</label>
                        <select name="sort">
                            @foreach($searchFields as $field)
                                <option value="{{ $field }}">{{str_replace("_", " ", $field)}}</option>
                            @endforeach
                        </select>
                        <input type="submit" class="ph-button ph-button-standard tile-all-columns">
                    </form>
                </div>
            </div>
        </div>
    </section>

</div>
<x-top-button></x-top-button>

@endsection
