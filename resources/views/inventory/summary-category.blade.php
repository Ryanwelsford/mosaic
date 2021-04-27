@extends('layout')
@section('title', $title)
@section("tools")
<button class="bar-tool-button" onclick="openOrCloseModal('search-modal')" name="save" value="save"><span class="mobile-hidden">Find</span> <i class="fas fa-search-location"></i></button>
@endsection
@section('content')
<div class="grid-container">
    <div class="main-tile tile-all-columns center-column">
        <div class="full-width">
            <h2 class="tile-title tile-all-columns ">Count Summary: {{ $category }}</h2>
            <div class="grid-2-col-wide">
                <label>Store: {{ $store->store_name }} - {{ $store->number }}</label>
                <label>Count Date: {{ $inventory->created_at->format("d M Y") }}</label>
                <label>{{ $category }} Value: £{{number_format($sum,2 )}}</label>

                <label>Total Cases: {{ number_format($quantity,2 ) }}</label>
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
                    <td>£{{ number_format(($details->pivot->quantity / $details->units->quantity) * $details->units->price,2)  }}</td>
                    <td>{{ number_format($details->pivot->quantity / $details->units->quantity, 2) }}</td>
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


            <div class="tile-all-columns center-column margin-top">
                <a href="{{ route("inventory.summary", [$inventory->id]) }}" class="ph-button ph-button-standard">Back</a>
            </div>
        </div>
    </div>
</div>

<x-tools.find-modal model="products"></x-tools.find-modal>
@endsection
