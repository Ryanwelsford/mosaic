@extends('layout')
@section('title', $title)

@section('content')


<div class="center-column">
    <div class="main-tile tile-all-columns center-column mono-tile">
        <form method="POST" action="{{ route("receiving.new") }}" id="form_receiving" class="center-column">
            @csrf

            <div class="grid-2-col-wide active-tab tab">
                <h2 class="tile-title tile-all-columns ">Receipt Details</h2>
                <input name="id" type="hidden" value="@if(isset($receipt->id)) {{ $receipt->id }} @endif">

                <label>Reference: @error('reference') <span class="error-text">*</span> @enderror</label>
                <div>
                    <input name="reference" class="@error('reference') input-error @enderror"type="text" value="@if(isset($receipt->reference)){{ $receipt->reference }} @endif" placeholder="Enter a reference">
                    @error('reference')
                            <div class="small-error-text error-text">{{ $message }} </div>
                    @enderror
                </div>

                <label>Receipt Date: </label>
                <div>
                    <input name="date" type="date" value="@if(isset($receipt->date) && (!$receipt instanceof stdClass)){{$receipt->getdate()->format("Y-m-d")}}@else{{$today->format("Y-m-d")}}@endif" placeholder="Enter a reference" min="@if(isset($today)){{$today->format("Y-m-d")}}@endif">
                </div>

                <label>Menu:</label>
                <select name="menu_id">
                    @foreach($menus as $key => $option)
                        <option value="{{$option->id}}"@if(isset($order->menu_id) && $order->menu_id == $option->id) {{ "selected" }} @endif>{{ ucwords($option->name) }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        <div class="tile-all-columns center-column margin-top">
                <button form="form_receiving" type="submit" class="ph-button ph-button-standard">Next</button>
        </div>

        <p class="right-aligned">Looking to edit a receipt click <a href="{{route('receiving.view')}}">here</a></p>
    </div>
</div>
@endsection
