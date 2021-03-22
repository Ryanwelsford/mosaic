@extends('layout')
@section('title', $title)

@section('content')


<div class="center-column">
    <div class="main-tile tile-all-columns center-column mono-tile">
        <form method="POST" action="{{ route("menu.new") }}" id="form_menu" class="center-column">
            @csrf

            <div class="grid-2-col-wide active-tab tab">
                <h2 class="tile-title tile-all-columns ">Menu Description</h2>
                <input name="menu[id]" type="hidden" value="@if(isset($menu->id)) {{ $menu->id }} @endif">
                <input name="copy_id" type="hidden" value="@if(isset($menuCopy->id)) {{ $menuCopy->id }} @endif">
                <label>Name: @error('menu.name') <span class="error-text">*</span> @enderror</label>
                <div>
                    <input name="menu[name]" class="@error('menu.name') input-error @enderror"type="text" value="@if(isset($menu->name)){{ $menu->name }} @endif">
                    @error('menu.name')
                            <div class="small-error-text error-text">{{ $message }} </div>
                    @enderror
                </div>

                <label>Description: @error('description') <span class="error-text">*</span> @enderror</label>
                <section>
                    <textarea  name="menu[description]" placeholder="Enter menu description if required" class="@error('description') input-error @enderror">@if(isset($menu->description)){{$menu->description}} @endif</textarea>
                    @error('description')
                            <div class="small-error-text error-text">{{ $message }} </div>
                        @enderror
                </section>

                <label>Status:</label>
                <select name="menu[status]">
                    @foreach($statuses as $key => $option)
                        <option @if(isset($menu->status) && $menu->status == $option) {{ "selected" }} @endif>{{ $option }}</option>
                    @endforeach
                </select>

            </div>
        </form>

        <div class="tile-all-columns center-column margin-top">
            @if(isset($menuCopy) && $menuCopy != false)
                <button form="form_menu" type="submit" class="ph-button ph-button-standard">Copy Products</button>
            @else
                <button form="form_menu" type="submit" class="ph-button ph-button-standard">Assign Products</button>
            @endif
        </div>

        <p class="right-aligned">Need to copy a menu instead click <a href="{{route('menu.view')}}">here</a></p>
    </div>
</div>
@endsection
