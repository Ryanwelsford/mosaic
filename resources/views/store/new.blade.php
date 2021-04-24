@extends('layout')
@section('title', $title)

@section('content')
<div class="center-column">
    <div class="main-tile tile-all-columns center-column mono-tile">
        <form method="POST" action="{{ route("store.new") }}" id="form_store" class="center-column">
            @csrf

            <div class="grid-2-col-wide active-tab tab">
                <h2 class="tile-title tile-all-columns">Store Details</h2>

                <x-inputs.hidden name="id" :value="$store"></x-inputs.hidden>

                <x-label-error label="store_name"></x-label-error>
                <x-inputs.text inputName="store_name" error="name" :value="$store"></x-inputs.text>

                <x-label-error label="Hut Number" error="number" ></x-label-error>
                <x-inputs.number inputName="number" error="number" :value="$store"></x-inputs.number>

                <x-label-error label="Address" error="address1"></x-label-error>
                <div class="spaced-inputs">
                    <x-inputs.text class="margin-blocked" inputName="address1" error="address1" :value="$store" :errorRequired="false"></x-inputs.text>
                    <x-inputs.text inputName="address2" error="address1" :value="$store" :errorRequired="false"></x-inputs.text>
                    <x-inputs.text inputName="address3" error="address1" :value="$store" customMessage="At least 1 address field is required"></x-inputs.text>
                </div>

                <x-label-error label="Post Code" error="postcode"></x-label-error>
                <x-inputs.text inputName="postcode" error="postcode" :value="$store"></x-inputs.text>

                <h2 class="tile-title tile-all-columns">Login Information</h2>

                <x-label-error label="password"></x-label-error>
                <x-inputs.text inputName="password" error="password" :value="$store" type="password"></x-inputs.text>

                <x-label-error label="Confirm Password" error="password_confirmation"></x-label-error>
                <x-inputs.text inputName="password_confirmation" error="password_confirmation" :value="$store" type="password"></x-inputs.text>

            </div>
        </form>

        <div class="tile-all-columns center-column margin-top">
            <button form="form_store" type="submit" class="ph-button ph-button-important">Submit</button>
        </div>
    </div>
</div>
@endsection
