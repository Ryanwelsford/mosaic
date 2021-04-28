@extends('layout')
@section('title', $title)
@section("tools")
<button class="bar-tool-button" onclick="openOrCloseModal('search-modal')" name="save" value="save"><span class="mobile-hidden">Find</span> <i class="fas fa-search-location"></i></button>
@endsection
@section('content')
<div class="grid-container">

    <div class="main-tile">
        <h2>Count Summary: {{ $category }}</h2>
        <div class="grid-2-col-wide display-flex">
            <label>Store: {{ $store->store_name }} - {{ $store->number }}</label>
            <label>Count Date: {{ $inventory->created_at->format("d M Y") }}</label>

            <label class="margin-top-2">{{ $category }} Value: £{{number_format($sum,2 )}}</label>
            <label>Total Cases: {{ number_format($quantity,2 ) }}</label>
        </div>
    </div>

    <div class="main-tile">
        <h2>Cases by subcategory</h2>
        <div id="piechart" class="chart center-column"></div>
    </div>

    <div class="main-tile">
        <h2>Value by subcategory</h2>
        <div id="piechart2" class=" chart center-column"></div>
    </div>

    <div class="main-tile tile-all-columns center-column">
        <div class="full-width">

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
<script type="text/javascript">

    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(onload);

    function drawChart() {

      var data = google.visualization.arrayToDataTable(<?php echo $chartData1;?>);

      let options = defaultOptions;
      options.title = "Inventory Cases by Subcategory";

      var chart = new google.visualization.PieChart(document.getElementById('piechart'));

      chart.draw(data, options);
    }

    function drawChart2() {
      var data = google.visualization.arrayToDataTable(<?php echo $chartData2;?>);

          var options = defaultOptions;
          options.title = "Inventory Value by Subcategory";

          var chart = new google.visualization.PieChart(document.getElementById('piechart2'));

          chart.draw(data, options);
    }

    function onload() {
        drawChart();
        drawChart2();
    }
</script>

<x-tools.find-modal model="products"></x-tools.find-modal>

@endsection
