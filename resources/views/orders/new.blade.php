@extends('layout')
@section('title', $title)

@section('content')


<div class="center-column">
    <div class="main-tile tile-all-columns center-column mono-tile">
        <form method="POST" action="{{ route("order.new") }}" id="form_order" class="center-column">
            @csrf

            <div class="grid-2-col-wide active-tab tab">
                <h2 class="tile-title tile-all-columns ">Order Details</h2>
                <input name="id" type="hidden" value="@if(isset($order->id)) {{ $order->id }} @endif">

                <label>Reference: @error('reference') <span class="error-text">*</span> @enderror</label>
                <div>
                    <input name="reference" class="@error('reference') input-error @enderror"type="text" value="@if(isset($menu->reference)){{ $menu->reference }} @endif" placeholder="Enter a reference">
                    @error('menu.name')
                            <div class="small-error-text error-text">{{ $message }} </div>
                    @enderror
                </div>

                <label>Delivery Date: @error('menu.name') <span class="error-text">*</span> @enderror</label>
                <div>
                    <input name="menu[name]" class="@error('menu.name') input-error @enderror"type="date" value="@if(isset($menu->name)){{ $menu->name }} @endif" placeholder="Enter a reference">
                    @error('menu.name')
                            <div class="small-error-text error-text">{{ $message }} </div>
                    @enderror
                </div>

                <label>Status:</label>
                <label>@if(isset($order->status)){{$order->status}}@else{{"Inactive"}}@endif</label>
                <input type="hidden" value="@if(isset($order->status)){{$order->status}}@else{{"Inactive"}}@endif">

                <label>Store:</label>
                <label>{{ $store->name }}</label>
                <input type="hidden" value="@if(isset($order->store_id)){{$order->store_id}}@endif">

                <label>Menu:</label>
                <select name="menu_id">
                    @foreach($menus as $key => $option)
                        <option value="{{$option->id}}"@if(isset($order->menu_id) && $order->menu_id == $option) {{ "selected" }} @endif>{{ ucwords($option->name) }}</option>
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
                <button form="form_order" type="submit" class="ph-button ph-button-standard">Enter Order</button>
        </div>

        <p class="right-aligned">Looking to edit an order click <a href="{{route('menu.view')}}">here</a></p>
    </div>
</div>
@endsection
