@extends('layout')
@section('title', $title)

@section('content')
<div class="grid-container">
    @for($i = 0; $i <= $dateDif; $i++)
    <div class="main-tile justified-center center-column">
        @if($i == 0)
        <h3>Date: {{ $starting_date->format("d M Y") }} </h3>
        @else
        <h3>Date: {{ $starting_date->addDays(1)->format("d M Y") }} </h3>
        @endif
        <h3>{{ $periodWeekDate->toStringIterate() }}</h3>
        <div class="grid-2-col-wide mobile-2-grid">
            <label>Holidays: </label>
            <label class="ph-checkbox-label">
                <input class="ph-checkbox" type="checkbox" name="menuListings[]" >
                <span class="checkmark"></span>
            </label>

            <label>Takeaway: </label>
            <label class="ph-checkbox-label">
                <input class="ph-checkbox" type="checkbox" name="menuListings[]" >
                <span class="checkmark"></span>
            </label>
        </div>

    </div>
    @endfor

    <div class="tile-all-columns center-column">
        <input type="submit" class="ph-button ph-button-important"
    </div>
</div>

@endsection
