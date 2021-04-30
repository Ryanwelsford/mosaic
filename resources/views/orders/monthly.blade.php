@extends('layout')
@section('title', $title)
@section('content')

<div class="grid-container">


    <div class="main-tile">

        <h2>Order Summary</h2>
        <div class="grid-2-col-wide display-flex">
            <label>Starting from: {{ $startDate->format('jS M Y') }}</label>
            <label>Ending on: {{ $endDate->format('jS M Y') }}</label>

            <label class="margin-top-2">Total Order Value: £{{ number_format($totalValue, 2) }} </label>
            <label>Total Orders Placed: {{ $orders->count() }}</label>
        </div>
    </div>

    <div class="main-tile">

        <h2>Order Value by Date</h2>
        @if(count($orders) > 1)
            <div id="linechart" class="chart center-column"></div>
        @else
            <label>Order information not available</label>
        @endif
    </div>

    <div class="main-tile">

        <h2>Order Value by Date</h2>
        @if(count($orders) > 1)
            <div id="stackedchart" class="chart center-column"></div>
        @else
            <label>Order information not available</label>
        @endif
    </div>

    <div class="main-tile tile-all-columns">
        <div class="full-width center-column">
            <h2 class="tile-title tile-all-columns ">Order Details</h2>
            @if($orders->count() >= 1)
            <table id="findable" class="wide-table full-width reduced-table">
                <tr>
                    <th>Reference</th>
                    <th>Delivery Date</th>
                    <th>Value</th>
                    <th>Summary</th>
                </tr>

                @foreach($orders as $order)
                <tr>
                    <td>{{ $order->reference }}</td>
                    <td>{{ $order->getDeliveryDate()->format('d M Y') }}</td>
                    <td>£{{ $order->total }}</td>
                    <td><a href="{{ route('order.summary', ['id' => $order->id]) }}"class="ph-button ph-button-standard table-button center">@include("icons.summary")</a></td>
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

        var data = google.visualization.arrayToDataTable(<?php echo $chartData1;?>);

        let options = defaultOptions;
        options.title = "Order Value by Date";
        options.curveType = 'function';
        options.vAxis =  {title: 'Order Total in £'}
        var chart = new google.visualization.ColumnChart(document.getElementById('linechart'));

        chart.draw(data, options);
    }

    function drawChart1() {

        var data = google.visualization.arrayToDataTable(<?php echo $chartData2;?>);

        let options = defaultOptions;
        options.title = "Order Categories by Date";
        options.curveType = 'function';
        options.vAxis =  {title: 'Order Category Total in £'}
        //options.isStacked = true;
        var chart = new google.visualization.ColumnChart(document.getElementById('stackedchart'));

        chart.draw(data, options);
    }

    function onload() {
        drawChart();
        drawChart1();
    }
  </script>
@endsection
