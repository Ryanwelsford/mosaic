@extends('layout')
@section('title', $title)

@section('content')


<div class="center-column">
    <div class="main-tile tile-all-columns center-column mono-tile">
        <form method="GET" action="{{ $route}}" id="form" class="center-column background-form">

            <div class="grid-2-col-wide">
                <h2 class="tile-title tile-all-columns ">{{ $heading }}</h2>

                <label>{{ $label }}</label>
                <input class="margin-top" name="date" type="date" required>

            </div>
        </form>

        <div class="tile-all-columns center-column margin-top">
                <button form="form" type="submit" class="ph-button ph-button-standard">Next</button>
        </div>

    </div>
</div>
@endsection
