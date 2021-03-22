@extends('layout')
@section('title', $title)

@section('content')
<div class="center-column">
    <div class="main-tile tile-all-columns center-column mono-tile">
        <form method="POST" action="{{ route("wastelist.new") }}" id="form_wastelist" class="center-column">
            @csrf

            <div class="grid-2-col-wide active-tab tab">
                <h2 class="tile-title tile-all-columns">Waste List Details</h2>
                <x-inputs.hidden name="id" :value="$wastelist"></x-inputs.hidden>



                <x-label-error label="name"></x-label-error>
                <!--passing in a variable object or array can be done using : rather than just error= you would do :error=-->
                <x-inputs.text inputName="name" :value="$wastelist" error="name"></x-inputs.text>


                <x-label-error label="description"></x-label-error>

                <x-inputs.textarea inputName="description" :value="$wastelist" error="description"></x-inputs.textarea>

            </div>
        </form>

        <div class="tile-all-columns center-column margin-top">
            <button form="form_wastelist" type="submit" class="ph-button ph-button-important">Submit</button>
        </div>
    </div>
</div>
@endsection
