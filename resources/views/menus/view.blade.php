@extends('layout')
@section('title', $title)

@section("tools")
    <button class="bar-tool-button" onclick="searchModal()">@include("icons.search")</button>
@endsection

@section('content')
<div class="grid-container">
    <div class="main-tile tile-all-columns center-column">
        <form class="search-form grid-2-col-wide table-width-match" method="GET" action="{{ route("menu.view") }}">
            <label>Search Menus</label>
            <div class="search-with-button">
                <input name="search" type ="text" class=""  id="search-bar" placeholder="Search here" value="@if(isset($search)){{$search}}@endif">
                <button type ="submit" class="ph-button ph-button-standard">Search</button>
            </div>
        </form>

        @if($menus->count() < 1 && !isset($search))
            <p>No menus currently exist, create a new menu <a href="{{ route("menu.new") }}">here</a></p>
        @endif

        @if(isset($search))
            <p>Displaying {{$menus->count()}} results for... <span class="italics">{{ $search }}</span> @if(isset($sort)) {{ "sorted by ".$sort }} @endif</p>
        @endif
    </div>
    @if($menus->count() >= 1)
    <div class="main-tile tile-all-columns center-column">
        <h2>Menu Details</h2>

        @if($menus->count() >= 1)

        @if(isset($menus) && $response != '')
            <div class ="confirmation-banner confirmation-message margin-bottom-2 full-width">
                <h3>{{ $response }} <button onclick="closeDiv(event)" class="close-X">X</button></h3>
            </div>
        @endif
        
        <table class="wide-table full-width reduced-table">
            <th>
                <p class="mob-hidden">Menu Name</p>
                <p class="mobile-only">Details</p>
            </th>
            <th class="mob-hidden">
                Description
            </th>
            <th class="mob-hidden">
                Status
            </th>
            <th class="mob-hidden">
                Created on
            </th>
            <th>
                Options
            </th>

            @foreach($menus as $menu)
                <tr>
                    <td>
                        {{ $menu->name }}
                        <p class='mobile-only'>{{ $menu->created_at->format('d/M/Y') }}</p>
                    </td>

                    <td class="mob-hidden">
                        @if(isset($menu->description))
                            {{ $menu->description }}
                        @else
                            {{ "Menu description is not set" }}
                        @endif
                    </td>

                    <td class="mob-hidden">
                        {{ $menu->status }}
                    </td>

                    <td class="mob-hidden">
                        {{ $menu->created_at->format('d/M/Y') }}
                    </td>

                    <td>
                        <div class="table-button-holder">
                            <a href="{{ route('menu.new', ['id' => $menu->id]) }}"class="ph-button ph-button-standard table-button">@include("icons.edit")</a>
                            <a href="{{ route('menu.new', ['copy_id' => $menu->id]) }}"class="ph-button ph-button-standard table-button">@include("icons.copy")</a>
                            <a href="{{ route('menu.assign', ['menu_id' => $menu->id]) }}" class="ph-button ph-button-standard table-button" type="submit">@include("icons.add")</a>

                            <form class="table-button" method="POST" action="{{ route("menu.destroy", $menu) }}">
                                <button class="ph-button ph-button-standard ph-button-important table-button" type="submit">@include("icons.delete")</button>
                                @csrf
                                @method('delete')
                            </form>

                        </div>

                    </td>
                </tr>

            @endforeach
        </table>
        @endif
    </div>
    @endif
    <x-tools.search-modal model="menu" action='menu.view' search="{{ $search }}" :fields="$searchFields"></x-tools.search-modal>
</div>

@endsection
