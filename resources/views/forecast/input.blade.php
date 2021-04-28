@extends('layout')
@section('title', $title)

@section('content')
<form class="grid-container" action="{{ route('forecasting.new') }}" method="POST">
    @csrf
    @for($i = 0; $i <= $dateDif; $i++)
    <div class="main-tile center-column">
        @if($i == 0)
        <h3>Date: {{ $starting_date->format("l jS M Y") }}</h3>
        @else
        <h3>Date: {{ $starting_date->addDays(1)->format("l jS M Y") }}</h3>
        @endif

        <div class="grid-2-col-wide mobile-2-grid">
            <label class="tile-all-columns">{{ $periodWeekDate->toStringIterate() }}</label>
            <label>Forecast: </label>
            <input name="forecast[id][]" type="hidden" value="@if(isset($mapped[$starting_date->format('Y-m-d')])){{$mapped[$starting_date->format('Y-m-d')]->id}}@endif">
            @if(isset($mapped[$starting_date->format('Y-m-d')]))
            <input name="forecast[value][]" type="number" value="{{ $mapped[$starting_date->format('Y-m-d')]->value }}" min="0" step="1">
            @else
            <input name="forecast[value][]" type="number" value="0" min="0" step="1">
            @endif

            <input name="forecast[date][]" type="hidden" value="{{ $starting_date->format('Y-m-d') }}">
        </div>

    </div>
    @endfor

    <div class="tile-all-columns center-column">
        <button type="submit" class="ph-button ph-button-standard ph-button-important">@include('icons.book')</button>
    </div>
</div>

@endsection
