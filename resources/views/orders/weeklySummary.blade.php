@extends('layout')
@section('title', $title)
@section('content')

<div class="grid-container">

    <div class="main-tile">
        <h2>Orders Summary</h2>
        <div class="grid-2-col-wide display-flex">
            <label>Starting from: {{ $startDate->format('jS M Y') }}</label>
            <label>Ending on: {{ $endDate->format('jS M Y') }}</label>

            <label class="margin-top-2">Total Orders Value: £{{ $sum }}</label>
            <label>Total Orders Cases: {{ $quantity }}</label>
        </div>
    </div>

    <div class="main-tile tile-2-4">
        <h2>Order by Category</h2>
        @if(count($orders) > 0)
            <div id="piechart" class="chart center-column"></div>
        @else
            <label >No order information available</label>
        @endif
    </div>

    <div class="main-tile tile-all-columns">
        <div class="full-width center-column">
            <h2 class="tile-title tile-all-columns ">Order Details</h2>
            @if(count($orders) > 0)
            <table id="findable" class="wide-table full-width reduced-table">
                <tr>
                    <th>Reference</th>
                    <th>Delivery Date</th>
                    <th>Value</th>
                    <th>Summary</th>
                </tr>

                @foreach($orders as $key => $order)
                    <tr>
                        <td>{{ $order->reference }}</td>
                        <td>{{ $order->getDeliveryDate()->format('jS M Y') }}</td>
                        <td>£{{ $order->total }}</td>
                        <td>
                            <a class="ph-button ph-button-standard table-button center" href="{{ route('order.summary', ['id' => $order->id]) }}">@include('icons.summary')</a>
                        </td>
                    </tr>
                @endforeach
            </table>

            @else
            <h3 class="centered">No order information found</h3>
            @endif
        </div>
    </div>

</div>

<script type="text/javascript">

    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(onload);

    function drawChart() {

      var data = google.visualization.arrayToDataTable(<?php echo $chartData;?>);

      let options = defaultOptions;
      options.title = "Total Weekly Order Value by Category";

      var chart = new google.visualization.PieChart(document.getElementById('piechart'));

      chart.draw(data, options);
    }

    function onload() {
        drawChart();
    }
  </script>
@endsection
