@extends('layout')
@section('title', $title)

@section('content')


<div class="center-column">
    <div class="main-tile tile-all-columns center-column mono-tile">
        <form method="GET" action="{{ route("forecasting.new") }}" id="form_menu" class="center-column background-form">

            <div class="grid-2-col-wide active-tab tab">
                <h2 class="tile-title tile-all-columns ">Select Date Range</h2>



                <div class="center-column">
                    <label>Starting from</label>
                    <input class="margin-top" name="starting_date" type="date" min="{{ $date->format("Y-m-d") }}" value="{{ $date->format("Y-m-d") }}">
                </div>

                <div class="center-column">
                    <label>Ending at</label>
                    <input class="margin-top" name="ending_date" type="date" min="{{ $date->format("Y-m-d") }}" value="{{ $weekAfterDate->format("Y-m-d") }}">
                </div>

            </div>
        </form>

        <div class="tile-all-columns center-column margin-top">
                <button form="form_menu" type="submit" class="ph-button ph-button-standard">Next</button>
        </div>

        <p class="right-aligned">Some kind of message <a href="{{route('menu.view')}}">here</a></p>
    </div>
</div>
@endsection
