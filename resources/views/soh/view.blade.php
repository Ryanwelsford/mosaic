@extends('layout')
@section('title', $title)

@section("tools")
    <button class="bar-tool-button" onclick="searchModal()"><span class="mobile-hidden">Search</span> <i class="fas fa-search"></i></button>
@endsection

@section('content')
<div class="grid-container">
    <div class="main-tile tile-all-columns center-column">
        <h2>Stock on Hand Details</h2>
        <form class="search-form grid-2-col-wide table-width-match" method="GET" action="{{ route("soh.view") }}">
            <label>Search Receipts</label>
            <div class="search-with-button">
                <input name="search" type ="text" class=""  id="search-bar" placeholder="Search here" value="@if(isset($search)){{$search}}@endif">
                <button type ="submit" class="ph-button ph-button-standard">Search</button>
            </div>
        </form>

        @if(isset($response) && $response != '')
            <div class ="confirmation-banner confirmation-message margin-bottom-2 full-width">
                <h3>{{ $response }} <button onclick="closeDiv(event)" class="close-X">X</button></h3>
            </div>
        @endif

        @if($sohs->count() < 1 && !isset($search))
            <p>No counts currently exist, create a new receipt <a href="{{ route("receiving.new") }}">here</a></p>
        @endif

        @if(isset($search))
            <p>Displaying {{$sohs->count()}} results for... <span class="italics">{{ $search }}</span> @if(isset($sort)) {{ "sorted by ".$sort }} @endif</p>
        @endif

        @if($sohs->count() >= 1)
        <table class="wide-table full-width reduced-table">
            <th class="">
                Reference
            </th>
            <th class="">
                Created on
            </th>
            <th>
                Options
            </th>

            @foreach($sohs as $soh)
                <tr>
                    <td class="">
                        no reference yet
                    </td>

                    <td class="">
                        {{ $soh->created_at->format('d M Y') }}
                    </td>

                    <td>
                        <div class="table-button-holder">
                            <a href="{{ route('soh.new', ['id' => $soh->id]) }}"class="ph-button ph-button-standard table-button">@include("icons.edit")</a>
                            <form method="POST" action="{{ route("soh.destroy", $soh) }}" class="table-button">
                                <button class="ph-button ph-button-standard ph-button-important table-button" type="submit">Delete</button>
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

    <x-tools.search-modal model="stock on hand" action='soh.view' search="{{ $search }}" :fields="$searchFields"></x-tools.search-modal>

</div>

@endsection
