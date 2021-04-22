@extends('layout')
@section('title', $title)

@section("tools")
    <button class="bar-tool-button" onclick="searchModal()"><span class="mobile-hidden">Search</span> <i class="fas fa-search"></i></button>
@endsection

@section('content')
<div class="grid-container">
    <div class="main-tile tile-all-columns center-column">
        <h2>Order Details</h2>
        <form class="search-form grid-2-col-wide table-width-match" method="GET" action="{{ route("receiving.view") }}">
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

        @if($receipts->count() < 1 && !isset($search))
            <p>No receipts currently exist, create a new receipt <a href="{{ route("receiving.new") }}">here</a></p>
        @endif

        @if(isset($search))
            <p>Displaying {{$receipts->count()}} results for... <span class="italics">{{ $search }}</span> @if(isset($sort)) {{ "sorted by ".$sort }} @endif</p>
        @endif

        @if($receipts->count() >= 1)
        <table class="wide-table full-width reduced-table">
            <th class="mob-hidden">
                Receipt #
            </th>
            <th class="">
                Reference
            </th>
            <th class="">
                Created on
            </th>
            <th class="">
                Date
            </th>
            <th>
                Options
            </th>

            @foreach($receipts as $receipt)
                <tr>
                    <td class="mob-hidden">
                        {{ $receipt->id }}
                    </td>

                    <td class="">
                        {{ $receipt->reference }}
                    </td>

                    <td class="">
                        {{ $receipt->created_at->format('d M Y') }}
                    </td>

                    <td class="">
                        {{ $receipt->getDate()->format('d M Y') }}
                    </td>

                    <td>
                        <div class="table-button-holder">
                            <a href="{{ route('receiving.new', ['id' => $receipt->id]) }}"class="ph-button ph-button-standard table-button">Edit</a>
                            <a href="{{ route('receiving.summary', [$receipt]) }}"class="ph-button ph-button-standard table-button">Summary <i class="fas fa-clipboard-list"></i></a>
                            <form method="POST" action="{{ route("receiving.destroy", $receipt) }}" class="table-button">
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

    <section class="modal" id="search-modal">
        <div class="modal-internal small-modal">
            <div class="modal-title">Search Menus <button onclick="searchModal()" class="close-X">X</button></div>
            <div class="modal-content vert-center">
                <div class="modal-center">
                    <form class="search-form grid-2-col-wide centered" method="GET" action="{{ route("receiving.view") }}">
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

</div>
<x-top-button></x-top-button>

@endsection
