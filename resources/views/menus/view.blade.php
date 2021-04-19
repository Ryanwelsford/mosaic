@extends('layout')
@section('title', $title)

@section("tools")
    <button class="bar-tool-button" onclick="searchModal()"><span class="mobile-hidden">Search</span> <i class="fas fa-search"></i></button>
@endsection

@section('content')
<div class="grid-container">
    <div class="main-tile tile-all-columns center-column">
        <h2>Menu Details</h2>
        <form class="search-form grid-2-col-wide table-width-match" method="GET" action="{{ route("menu.view") }}">
            <label>Search Menus</label>
            <div class="search-with-button">
                <input name="search" type ="text" class=" "  id="search-bar" placeholder="Search here" value="@if(isset($search)){{$search}}@endif">
                <button type ="submit" class="ph-button ph-button-standard">Search</button>
            </div>
        </form>

        @if($menus->count() < 1 && !isset($search))
            <p>No menus currently exist, create a new menu <a href="{{ route("menu.new") }}">here</a></p>
        @endif

        @if(isset($search))
            <p>Displaying {{$menus->count()}} results for... <span class="italics">{{ $search }}</span></p>
        @else

        <table class="wide-table">
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
                            <a href="{{ route('menu.new', ['id' => $menu->id]) }}"class="ph-button ph-button-standard table-button">Edit<img src="/images/icons/edit-48-black.png"></a>
                            <a href="{{ route('menu.new', ['copy_id' => $menu->id]) }}"class="ph-button ph-button-standard table-button">Copy<img src="/images/icons/copy-48-black.png"></a>
                            <a href="{{ route('menu.assign', ['menu_id' => $menu->id]) }}" class="ph-button ph-button-standard table-button" type="submit">Add to<img src="/images/icons/add-list-48-black.png"></a>

                            <form method="POST" action="{{ route("menu.destroy", $menu) }}">
                                <button class="ph-button ph-button-important table-button" type="submit">Delete</button>
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

    <section class="modal" id="search-modal">
        <div class="modal-internal small-modal">
            <div class="modal-title">Search Menus <button onclick="searchModal()" class="close-X">X</button></div>
            <div class="modal-content vert-center">
                <div class="modal-center">
                    <form class="search-form grid-2-col-wide centered" method="GET" action="{{ route("menu.view") }}">
                        <label>Search Menus</label>
                        <input name="search" value="@if(isset($search)) {{ $search }} @endif" type ="text" class=" "  id="search-bar" placeholder="Search here">
                        <label>Sort by</label>
                        <select name="sort">
                            @foreach($searchFields as $field)
                                <option value="{{ $field }}">{{str_replace("_", " ", $field)}}</option>
                            @endforeach
                        </select>
                        <input type="submit" class="ph-button ph-button-standard tile-all-columns">
                    </form>
                </div>
            </div>
        </div>
    </section>

</div>
<x-top-button></x-top-button>

@endsection
