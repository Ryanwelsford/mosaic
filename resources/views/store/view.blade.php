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

    @if($stores->count() < 1 && !isset($search))
            <p>No stores currently exist, create a new store <a href="{{ route("store.new") }}">here</a></p>
        @endif

        @if(isset($search))
            <p>Displaying {{$stores->count()}} of {{ $stores->total() }} results for... <span class="italics">{{ $search }}</span> @if(isset($sort)) {{ "sorted by ".$sort }} @endif</p>
        @endif
</div>

<div class="main-tile tile-all-columns center-column">
    <h2>Store Details</h2>
    @if(isset($response) && $response != '')
        <div class ="confirmation-banner confirmation-message margin-bottom-2 full-width">
            <h3>{{ $response }} <button onclick="closeDiv(event)" class="close-X">X</button></h3>
        </div>
    @endif

    <table class="wide-table full-width reduced-table"">
        <th>
            Name
        </th>
        <th class="mob-hidden">
            Email
        </th>
        <th>
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
                <td>
                    {{ $store->stores_number }}
                </td>
                <td class="mob-hidden">
                    {{ $store->getCreated($store->stores_created_at) }}
                </td>
                <td>
                    <div class="table-button-holder">
                        <a href="{{ route('store.new', ['id' => $store->stores_id ]) }}"class="ph-button ph-button-standard table-button">@include('icons.edit')</a>
                        <form method="POST" action="{{ route("store.destroy", $store->stores_id) }}">
                            <button class="ph-button ph-button-important table-button" type="submit">@include('icons.delete')</button>
                            @csrf
                            @method('delete')
                        </form>
                    </div>
                </td>
            </tr>

        @endforeach
    </table>

    {{ $stores->links('paginate.default', ["paginator" => $stores, "search" => $search, "sort" => $sort]) }}
</div>
</div>
<x-tools.search-modal model="Stores" action='store.view' search="{{ $search }}" :fields="$searchFields"></x-tools.search-modal>
@endsection
