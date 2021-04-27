@extends('layout')
@section('title', $title)
@section("tools")
<button class="bar-tool-button" onclick="openOrCloseModal('search-modal')" name="save" value="save"><span class="mobile-hidden">Find</span> <i class="fas fa-search-location"></i></button>
@endsection
@section('content')
<div class="grid-container">
    <div class="main-tile tile-all-columns center-column">
        <div class="full-width center-column">
            <h2 class="tile-title tile-all-columns ">Count Summary</h2>
            <div class="grid-2-col-wide display">
                <label>Store: {{ $store->store_name }} - {{ $store->number }}</label>
                <label>Count Date: {{ $inventory->created_at->format("d M Y") }}</label>
                <label>Count Value: £{{number_format($sum,2 )}}</label>
                <label>Total Cases: {{ number_format($quantity,2 ) }}</label>
                <label>Status: {{ $inventory->status }}</label>
            </div>

            <table id="findable" class="wide-table full-width reduced-table" >
                <th>Product Name</th>
                <th>Value</th>
                <th>Cases</th>


                @php
                    $subcategory = '';
                    if(isset($products[0])) {
                        $subcategory = $products[0]->subcategory;
                    }

                @endphp
                <tr>
                    <td class="span-table-rows" colspan="100%"><h3 class="table-breaker">{{ $subcategory }}</h3></td>
                </tr>
                @foreach($products as $details)
                    @if($details->subcategory != $subcategory)
                        @php
                            $subcategory = $details->subcategory
                        @endphp
                        <tr>
                            <td class="span-table-rows" colspan="100%"><h3 class="table-breaker">{{ $subcategory }}</h3></td>
                        </tr>

                    @endif
                <tr>
                    <td>{{ $details->name }}</td>
                    <td>£{{ $details->pivot->quantity * $details->units->price }}</td>
                    <td>{{ $details->pivot->quantity }}</td>
                </tr>
                @endforeach
                <tr>
                    <th></th>
                    <th>Total</th>
                    <th>Cases</th>
                </tr>
                <tr>
                    <td></td>
                    <td>£{{number_format($sum,2 )}}</td>
                    <td>{{ number_format($quantity,2 ) }}</td>
                </tr>
            </table>    
        </div>
    </div>
</div>

<x-tools.find-modal model="products"></x-tools.find-modal>
@endsection
