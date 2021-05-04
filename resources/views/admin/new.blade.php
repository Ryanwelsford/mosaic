@extends('layout')
@section('title', $title)

@section('content')
<div class="center-column">
    <div class="main-tile tile-all-columns center-column mono-tile">
        <form method="POST" action="{{ route("admin.new") }}" id="form_store" class="center-column">
            @csrf

            <div class="grid-2-col-wide ">
                <h2 class="tile-title tile-all-columns">Admin Details</h2>

                <x-inputs.hidden name="id" :value="$admin"></x-inputs.hidden>

                <x-label-error label="name"></x-label-error>
                <x-inputs.text inputName="name" error="name" :value="$admin"></x-inputs.text>

                <x-label-error label="email" error="email"></x-label-error>
                <x-inputs.text inputName="email" error="email" :value="$admin"></x-inputs.text>

                <h2 class="tile-title tile-all-columns">Login Information</h2>

                <x-label-error label="password"></x-label-error>
                <div id="password-holder" class="group-input">
                    <button onclick="revealPassword('password-holder')" class="input-internal" type="button"><i class="far fa-eye"></i></button>
                    <input type="password" name="password" class="@error('password') {{ "input-error" }}@enderror">
                    @error('password')
                        <div class="small-error-text error-text">{{ $message }}</div>
                    @enderror
                </div>

                <x-label-error label="Confirm Password" error="password_confirmation"></x-label-error>
                <div id="password_confirmation" class="group-input">
                    <button onclick="revealPassword('password_confirmation')" class="input-internal" type="button"><i class="far fa-eye"></i></button>
                    <input type="password" name="password_confirmation" class="@error('password') {{ "input-error" }}@enderror">
                    @error('password_confirmation')
                        <div class="small-error-text error-text">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </form>

        <div class="tile-all-columns center-column margin-top">
            <button form="form_store" type="submit" class="ph-button ph-button-important">Submit</button>
        </div>
    </div>
</div>
@endsection
