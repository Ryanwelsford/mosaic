@extends('layout')
@section('title', $title)

@section('content')


<div class="center-column">
    <div class="main-tile tile-all-columns center-column ">
        <form method="GET" action="{{ route("forecasting.new") }}" id="form_menu" class="center-column background-form">

            <div class="grid-2-col-wide center-column">
                <h2 class="tile-title tile-all-columns ">Select Date Range</h2>



                <div class="center-column">
                    <label>Starting from</label>
                    <input class="margin-top" name="starting_date" type="date" min="{{ $today->format("Y-m-d") }}" value="{{ $date->format("Y-m-d") }}">
                </div>

                <div class="center-column">
                    <label>Ending at</label>
                    <input class="margin-top" name="ending_date" type="date" min="{{ $today->format("Y-m-d") }}" value="{{ $weekAfterDate->format("Y-m-d") }}">
                </div>

            </div>
        </form>

        <div class="tile-all-columns center-column margin-top">
                <button form="form_menu" type="submit" class="ph-button ph-button-standard">Next</button>
        </div>

        <p class="right-aligned">Edit forecasts by entering in the date range you wish to edit</p>
    </div>
</div>
@endsection
