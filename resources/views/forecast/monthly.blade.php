@extends('layout')
@section('title', $title)
@section('content')

<div class="grid-container">

    <div class="main-tile">
        <h2>Forecast Summary</h2>
        <div class="grid-2-col-wide display-flex">
            <label>Starting from: {{ $starting_date->format('jS M Y') }}</label>
            <label>Ending at: {{ $ending_date->format('jS M Y') }}</label>

            <label class="margin-top-2">Total forecast Value: £{{number_format($forecast_total, 0)}}</label>
        </div>
    </div>

    <div class="main-tile tile-2-4">
        <h2>Forecast Totals by Week</h2>
        @if(count($weekly) > 0)
        <table id="findable" class="wide-table full-width reduced-table">
            <th>Breakdown</th>
            <th>Week Starting</th>
            <th>Forecast Total</th>

            <tbody>
                <tr>
                    @foreach($weekly as $week_start => $value)
                        <tr>
                            <td><a class=" ph-button ph-button-standard table-button center" href="{{ route("forecasting.week", [$value["date"]]) }}">@include('icons.summary')</a></td>
                            <td>{{ $week_start }}</td>
                            <td>£{{ number_format($value["value"],0) }}</td>
                        </tr>
                    @endforeach
                </tr>
            </tbody>
        </table>
        @else
        <h3 class="centered">No forecast information found</h3>
        @endif
    </div>

    <div class="main-tile tile-all-columns">
        <div class="full-width center-column">
            <h2 class="tile-title tile-all-columns ">Forecast by Day</h2>
            @if(count($forecasts) > 0)
            <table id="findable" class="wide-table full-width reduced-table">
                <tr>
                    <th>Date</th>
                    <th>Forecast</th>
                </tr>

                @foreach($forecasts as $forecast)
                    <tr>
                        <td>{{ $forecast->getDate()->format('l jS M Y') }}</td>
                        <td>£{{ number_format($forecast->value,0) }}</td>
                    </tr>
                @endforeach
            </table>


            <div class="tile-all-columns center-column margin-top">
                <a href="{{ route('forecasting.date') }}" class="ph-button ph-button-standard">@include('icons.edit')</a>
            </div>
            @else
            <h3 class="centered">No forecast information found</h3>
            @endif
        </div>
    </div>

</div>
@endsection
