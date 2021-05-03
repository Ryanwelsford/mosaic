@extends('layout')
@section('title', $title)
@section('content')

<div class="grid-container">
    <div class="main-tile flex-tile">

        <h2>Forecast by Day</h2>
        @if($forecasts->count() > 0)
        <table id="findable" class="wide-table full-width reduced-table">
            <tr>
                <th>Date</th>
                <th>Value</th>
            </tr>

            @foreach($forecasts as $forecast)
                <tr>
                    <td>{{ $forecast->getDate()->format('l jS M Y') }}</td>
                    <td>£{{ number_format($forecast->value, 0) }}</td>
                </tr>
            @endforeach

        </table>

        <div class="center-column highlight-box">
            <div>Weeks Forecast Total:</div>
            <div>£{{  number_format($forecastTotal, 0) }}</div>
        </div>

        <div class="auto-top">
            <p class="right-aligned">View monthly forecasts <a href="{{ route('forecasting.monthly', [$startDate->format('Y-m-d')]) }}">here</a></p>
        </div>

        @else
        <h3 class="centered">No forecast information found</h3>
        <div class="auto-top">
            <p class="right-aligned">Create forecasts <a href="{{ route("forecasting.new") }}">here</a></p>
        </div>
        @endif

    </div>
    <div class="main-tile tile-2-4 flex-tile">

        <h2>Current Inventory Summary</h2>
        @if(!is_null($inventory))
        <div class="dash-grid">
            <div id="piechart" class="chart center-column"></div>
            <div class="highlight-box">
                <div>Inventory Value:</div>
                <div>£{{  number_format($inventoryValue, 0) }}</div>
                <div class="margin-top-2">Inventory Case Count:</div>
                <div>{{  number_format($inventoryCount, 0) }}</div>
            </div>
        </div>

        <div class="auto-top">
            <p class="right-aligned">View full inventory <a href="{{ route("inventory.summary", [$inventory->id]) }}">here</a></p>
        </div>
        @else
        <h3 class="centered">No inventory information found</h3>

        <div class="auto-top">
            <p class="right-aligned">Create a new count <a href="{{ route("inventory.new") }}">here</a></p>
        </div>
        @endif
    </div>

    <div class="main-tile tile-1-3 flex-tile">

        <h2>Delivery Schedule</h2>
        @if($orders->count() > 0)
        <table class="wide-table full-width reduced-table">
            <tr>
                <th>Delivery Date</th>
                <th>Value</th>
                <th>Details</th>
            </tr>

            @foreach($orders as $order)
            <tr>
                <td>{{ $order->getDeliveryDate()->format('jS M Y') }}</td>
                <td>£{{ $order->total }}</td>
                <td><a href="{{ route('order.summary', ['id' => $order->id]) }}"class="ph-button ph-button-standard table-button center">@include("icons.summary")</a></td>
            </tr>
            @endforeach

        </table>

        <div class="auto-top">
            <p class="right-aligned">View Details <a href="{{ route("order.weekly") }}">here</a></p>
        </div>

        @else
        <h3 class="centered">No order information found</h3>
        <div class="auto-top">
            <p class="right-aligned">Create a new order <a href="{{ route("order.new") }}">here</a></p>
        </div>
        @endif




    </div>



    <div class="main-tile flex-tile">

        <h2>Waste by Day</h2>
        @if($totalWastes > 0)
        <div id="columnchart" class="chart center-column"></div>

            <div class="center-column highlight-box">
                <div>Total Wasted:</div>
                <div>£{{ number_format($wasteTotal,2) }}</div>
            </div>

        <div class="auto-top">
            <p class="right-aligned">View full waste data <a href="{{ route('waste.weekly', [$startDate->format('Y-m-d')]) }}">here</a></p>
        </div>

        @else
        <h3 class="centered">No waste information found</h3>
        <div class="auto-top">
            <p class="right-aligned">Input waste <a href="{{ route("waste.new") }}">here</a></p>
        </div>
        @endif

    </div>

</div>

<script>
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(onload);

    function drawChart() {

      var data = google.visualization.arrayToDataTable(<?php echo $wasteChartData;?>);

      let options = defaultOptions;
      options.title = "Waste Value by Day";

      var chart = new google.visualization.ColumnChart(document.getElementById('columnchart'));

      chart.draw(data, options);
    }

    function drawChart2() {
    var data = google.visualization.arrayToDataTable(<?php echo $inventoryChart;?>);

        var options = defaultOptions;
        options.title = "Inventory Value by Category";

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
  }

    function onload() {
        drawChart();
        drawChart2();
    }

</script>
@endsection
