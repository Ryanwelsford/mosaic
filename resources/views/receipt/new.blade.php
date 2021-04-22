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
                    <input name="reference" class="@error('reference') input-error @enderror"type="text" value="@if(isset($order->reference)){{ $order->reference }} @endif" placeholder="Enter a reference">
                    @error('reference')
                            <div class="small-error-text error-text">{{ $message }} </div>
                    @enderror
                </div>

                <label>Receipt Date: @error('order.delivery_date') <span class="error-text">*</span> @enderror</label>
                <div>
                    <input name="date" class="@error('order.delivery_date') input-error @enderror"type="date" value="@if(isset($order->delivery_date)){{$order->delivery_date->format("Y-m-d")}}@else{{$today->format("Y-m-d")}}@endif" placeholder="Enter a reference" min="{{$today->format("Y-m-d")}}">
                    @error('order.delivery_date')
                            <div class="small-error-text error-text">{{ $message }} </div>
                    @enderror
                </div>

                <label>Menu:</label>
                <select name="menu_id">
                    @foreach($menus as $key => $option)
                        <option value="{{$option->id}}"@if(isset($order->menu_id) && $order->menu_id == $option->id) {{ "selected" }} @endif>{{ ucwords($option->name) }}</option>
                    @endforeach
                </select>

                <label>Full Order Input Mode:</label>
                <label class="ph-checkbox-label justify-right">
                    <input class="ph-checkbox" type="checkbox" name="display_mode" value ="{{ true }}" >
                    <span class="checkmark"></span>
                </label>
            </div>
        </form>

        <div class="tile-all-columns center-column margin-top">
                <button form="form_receiving" type="submit" class="ph-button ph-button-standard">Enter Receipt</button>
        </div>

        <p class="right-aligned">Looking to edit an receipt click <a href="{{route('menu.view')}}">here</a></p>
    </div>
</div>
@endsection
