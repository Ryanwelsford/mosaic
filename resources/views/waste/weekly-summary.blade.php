@extends('layout')
@section('title', $title)
@section('content')

<div class="grid-container">

    <div class="main-tile">

        <h2>Waste Summary</h2>
        <div class="grid-2-col-wide display-flex">
            <label>Starting from: {{ $startDate->format('jS M Y') }}</label>
            <label>Ending on: {{ $endDate->format('jS M Y') }}</label>

            <label class="margin-top-2">Total Waste Value: £{{ number_format($totalValue, 2) }} </label>
            <label>Total Waste Entries: {{ $wastes->count() }}</label>
        </div>
    </div>

    <div class="main-tile">
        <h2>Waste by Day</h2>
        @if($wastes->count() > 0)
            <div id="columnchart" class="chart center-column"></div>
        @else
            <h3 class="centered">No order information available</h3>
        @endif
    </div>

    <div class="main-tile">
        <h2>Waste by Category</h2>

            <div id="piechart" class="chart center-column"></div>

            <h3 class="centered">No order information available</h3>

    </div>

    <div class="main-tile tile-all-columns">
        <div class="full-width center-column">
            <h2 class="tile-title tile-all-columns ">Waste Details</h2>
            @if($wastes->count() >= 1)
            <table id="findable" class="wide-table full-width reduced-table">
                <tr>
                    <th>Reference</th>
                    <th>Entry Date</th>
                    <th>Value</th>
                    <th>Cases</th>
                    <th>Summary</th>
                </tr>

                @foreach($wastes as $waste)
                <tr>
                    <td>{{ $waste->reference }}</td>
                    <td>{{ $waste->created_at->format("d M Y") }}</td>
                    <td>£{{ number_format($waste->total,2) }}</td>
                    <td>{{ number_format($waste->quantity,2) }}</td>
                    <td><a href="{{ route('waste.summary', [$waste->id ]) }}"class="ph-button ph-button-standard table-button center">@include("icons.summary")</a></td>
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
      options.title = "Waste Value by Day";
      options.color = "red";

      var chart = new google.visualization.ColumnChart(document.getElementById('columnchart'));

      chart.draw(data, options);
    }

    function drawChart2() {

        var data = google.visualization.arrayToDataTable(<?php echo $chartData2;?>);

        let options = defaultOptions;
        options.title = "Waste Value by Category";

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
    }

    function onload() {
        drawChart();
        drawChart2();
    }

</script>

@endsection
