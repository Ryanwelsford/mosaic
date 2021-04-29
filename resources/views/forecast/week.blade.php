@extends('layout')
@section('title', $title)
@section("tools")
@endsection
@section('content')

<div class="grid-container">

    <div class="main-tile">
        <h2>Forecast Summary</h2>
        <div class="grid-2-col-wide display-flex">
            <label>Starting from: {{ $starting_date->format('jS M Y') }}</label>
            <label>Ending at: {{ $ending_date->format('jS M Y') }}</label>

            <label class="margin-top-2">Total forecast value: £{{$forecast_total}}</label>
        </div>
    </div>

    <div class="main-tile">
        <h2>Forecast by Day</h2>
        @if(count($forecasts) > 2)
            <div id="piechart" class="chart center-column"></div>
        @else
            <label>Forecast information not available</label>
        @endif
    </div>
    <div class="main-tile">
        <div class="full-width center-column">
            <h2 class="tile-title tile-all-columns ">Daily Forecasts</h2>

            <table id="findable" class="wide-table full-width reduced-table">
                <tr>
                    <th>Date</th>
                    <th>Value</th>
                </tr>

                @foreach($forecasts as $forecast)
                    <tr>
                        <td>{{ $forecast->getDate()->format('l jS M Y') }}</td>
                        <td>{{ number_format($forecast->value, 0) }}</td>
                    </tr>
                @endforeach
            </table>


            <div class="tile-all-columns center-column margin-top">
                <a href="{{ route('forecasting.date') }}" class="ph-button ph-button-standard">@include('icons.edit')</a>
            </div>
        </div>
    </div>

</div>
<script type="text/javascript">

  google.charts.load('current', {'packages':['corechart']});
  google.charts.setOnLoadCallback(onload);

  function drawChart() {

    var data = google.visualization.arrayToDataTable(<?php echo $chartData1;?>);

    let options = defaultOptions;
    options.title = "Forecast by Day";
    options.curveType = 'function';

    var chart = new google.visualization.LineChart(document.getElementById('piechart'));

    chart.draw(data, options);
  }

  function onload() {
      drawChart();
  }
</script>
@endsection
