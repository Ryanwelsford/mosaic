@extends('layout')
@section('title', $title)

@section("tools")
    <button class="bar-tool-button" onclick="searchModal()"><span class="mobile-hidden">Search</span> <i class="fas fa-search"></i></button>
@endsection

@section('content')

<div class="grid-container">
    <div class="main-tile tile-all-columns center-column">
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
    </div>
    
    @if($orders->count() >= 1)
    <div class="main-tile tile-all-columns center-column">
        <h2>Order Details</h2>

        @if(isset($response) && $response != '')
            <div class ="confirmation-banner confirmation-message margin-bottom-2 full-width">
                <h3>{{ $response }} <button onclick="closeDiv(event)" class="close-X">X</button></h3>
            </div>
        @endif

        @if($orders->count() >= 1)
        <table class="wide-table full-width reduced-table">
            <th class="mob-hidden">
                Order #
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
                    <td class="mob-hidden">
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
                                <a href="{{ route('order.summary', ['id' => $order->id]) }}"class="ph-button ph-button-standard table-button">@include("icons.summary")</a>
                                <a target="_blank" href="{{ route('order.print', ['id' => $order->id]) }}"class="ph-button ph-button-standard table-button">@include("icons.print")</a>
                            @else
                                <a href="{{ route('order.new', ['id' => $order->id]) }}"class="ph-button ph-button-standard table-button">@include("icons.edit")</a>

                                <form method="POST" action="{{ route("order.destroy", $order) }}" class="table-button">
                                    <button class="ph-button ph-button-standard ph-button-important table-button" type="submit">@include("icons.delete")</button>
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
    @endif


    <x-tools.search-modal model="Orders" action='order.view' search="{{ $search }}" :fields="$searchFields"></x-tools.search-modal>

</div>

@endsection
