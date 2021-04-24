@extends('layout')
@section('title', $title)

@section('tools')
    <button class="bar-tool-button" onclick="searchModal()"><span class="mobile-hidden">Search</span> <i class="fas fa-search"></i></button>
@endsection

@section('content')
<div class="grid-container">

<div class="main-tile tile-all-columns center-column">
    <form class="search-form grid-2-col-wide table-width-match" method="GET" action="{{ route("store.view") }}">
        <label>Search Stores</label>
        <div class="search-with-button">
            <input name="search" type ="text" class=" "  id="search-bar" placeholder="Search here" value="@if(isset($search)){{$search}}@endif">
            <button type ="submit" class="ph-button ph-button-standard">Search</button>
        </div>
    </form>
</div>
<div class="main-tile tile-all-columns center-column">
    <h2>Store Details</h2>
    <table class="wide-table">
        <th>
            <p class="mob-hidden">Name</p>
            <p class="mobile-only">Details</p>
        </th>
        <th class="mob-hidden">
            Email
        </th>
        <th class="mob-hidden">
            Hut Number
        </th>
        <th class="mob-hidden">
            Created At
        </th>
        <th>
            Options
        </th>

        @foreach($stores as $key => $store)
            <tr>
                <td>
                    {{ $store->stores_store_name }}
                </td>
                <td class="mob-hidden">
                    {{ $store->users_email }}
                </td>
                <td class="mob-hidden">
                    {{ $store->stores_number }}
                </td>
                <td class="mob-hidden">
                    {{ $store->getCreated($store->stores_created_at) }}
                </td>
                <td>
                    <div class="table-button-holder">
                        <a href="{{ route('store.new', ['id' => $store->stores_id ]) }}"class="ph-button ph-button-standard table-button">Edit<img src="/images/icons/edit-48-black.png"></a>
                        <form method="POST" action="{{ route("store.destroy", $store->stores_id) }}">
                            <button class="ph-button ph-button-important table-button" type="submit">Delete <img src="/images/icons/delete-48-black.png"></button>
                            @csrf
                            @method('delete')
                        </form>
                    </div>
                </td>
            </tr>

        @endforeach
    </table>
</div>
</div>
<section class="modal" id="search-modal">
    <div class="modal-internal small-modal">
        <div class="modal-title">Search Menus <button onclick="searchModal()" class="close-X">X</button></div>
        <div class="modal-content vert-center">
            <div class="modal-center">
                <form class="search-form grid-2-col-wide centered" method="GET" action="{{ route("store.view") }}">
                    <label>Search Menus</label>
                    <input name="search" value="@if(isset($search)){{$search}}@endif" type ="text" class=" "  id="search-bar" placeholder="Search here">
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
<x-top-button></x-top-button>
@endsection
