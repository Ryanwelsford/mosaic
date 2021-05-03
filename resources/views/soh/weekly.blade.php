@extends('layout')
@section('title', $title)
@section('content')

<div class="grid-container">

    <div class="main-tile">

        <h2>Stock on Hand Summary</h2>
        <div class="grid-2-col-wide display-flex">
            <label>Starting from: {{ $startDate->format('jS M Y') }}</label>
            <label>Ending on: {{ $endDate->format('jS M Y') }}</label>

            <label class="margin-top-2">Counts Entered: {{ $counts->count() }}</label>
        </div>
    </div>
    <div class="main-tile tile-2-4">
        <h2>Product Daily Counts</h2>
        <div id="linechart" class="chart center-column"></div>
    </div>


    <div class="main-tile tile-all-columns">
        <div class="full-width center-column">
            <h2 class="tile-title tile-all-columns ">Entry Details</h2>
            @if(true)
            <table id="findable" class="wide-table full-width reduced-table scrollable-table">
                <tr>
                    <th></th>
                    <th colspan="100%">Count Totals in Cases</th>
                </tr>
                <tr>
                    <th>Product</th>
                    <th>Mon</th>
                    <th>Tue</th>
                    <th>Wed</th>
                    <th>Thu</th>
                    <th>Fri</th>
                    <th>Sat</th>
                    <th>Sun</th>
                </tr>


                @foreach($countMap as $pid => $map)
                <tr>
                    <td>{{ $productMap[$pid]->name }}</td>

                    @foreach($days as $day)
                        <td>@if(isset($map[$day])) {{ round($map[$day],2) }}@else {{ "No Entry" }} @endif</td>
                    @endforeach
                </tr>
                @endforeach
            </table>

            @else
            <h3 class="centered">No order information found</h3>
            @endif
        </div>
    </div>

</div>
<script>
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(onload);

    function drawChart() {

      var data = google.visualization.arrayToDataTable(<?php echo $chartData1;?>);

      let options = defaultOptions;
      options.title = "Product Counts by Day";
      options.vAxis =  {title: 'Cases Count'}
      options.curveType = 'function';
      options.hAxis = {
                gridlines: {count: 5}
              }


      var chart = new google.visualization.LineChart(document.getElementById('linechart'));

      chart.draw(data, options);
    }

    function onload() {
        drawChart();
    }

</script>
@endsection
