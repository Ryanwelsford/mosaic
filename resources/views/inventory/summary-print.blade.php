@extends('layouts.print-layout')
@section('title', $title)

@section('content')
    <div class="main-tile tile-all-columns center-column full-width">
        <div class="full-width">
            <h2 class="tile-title tile-all-columns ">Count Summary</h2>
            <div class="grid-2-col-wide display">
                <label>Store: {{ $store->store_name }} - {{ $store->number }}</label>
                <label>Count Date: {{ $inventory->created_at->format("d M Y") }}</label>
                <label>Count Value: £{{number_format($sum,2 )}}</label>
                <label>Total Cases: {{ number_format($quantity,2 ) }}</label>
                <label>Status: {{ $inventory->status }}</label>
            </div>


            <table id="findable" class="wide-table full-width reduced-table">
                <th>Category</th>
                <th>Value</th>
                <th>Cases</th>



                @foreach($catSummary as $category => $details)
                <tr>
                    <td>{{ $category }}</td>
                    <td>£{{ number_format($details['sum'], 2) }}</td>
                    <td>{{ number_format($details['quantity'],2) }}</td>
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
@endsection
