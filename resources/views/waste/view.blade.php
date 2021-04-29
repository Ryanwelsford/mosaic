@extends('layout')
@section('title', $title)

@section('tools')
    <button class="bar-tool-button" onclick="searchModal()">@include("icons.search")</button>
@endsection

@section('content')
<div class="grid-container">
    <div class="main-tile tile-all-columns center-column">
        <form class="search-form grid-2-col-wide table-width-match" method="GET" action="{{ route("waste.view") }}">
            <label>Search Waste</label>
            <div class="search-with-button">
                <input name="search" type ="text" class=""  id="search-bar" placeholder="Search here" value="@if(isset($search)){{$search}}@endif">
                <button type ="submit" class="ph-button ph-button-standard">Search</button>
            </div>
        </form>

        @if($wastes->count() < 1 && !isset($search))
        <p>No wastes currently exist, create a new waste <a href="{{ route("waste.new") }}">here</a></p>
    @endif

    @if(isset($search))
        <p>Displaying {{$wastes->count()}} results for... <span class="italics">{{ $search }}</span> @if(isset($sort)) {{ "sorted by ".$sort }} @endif</p>
    @endif
    </div>
    @if($wastes->count() >= 1)
    <div class="main-tile tile-all-columns center-column">
        <h2>Waste Details</h2>
        @if(isset($response) && $response != '')
                <div class ="confirmation-banner confirmation-message margin-bottom-2 full-width">
                    <h3>{{ $response }} <button onclick="closeDiv(event)" class="close-X">X</button></h3>
                </div>
            @endif

        @if($wastes->count() >= 1)
        <table class="wide-table full-width reduced-table">
            <th>
                Reference
            </th>
            <th>
                Type
            </th>
            <th>
                Created At
            </th>
            <th>
                Options
            </th>

            @foreach($wastes as $key => $waste)
                <tr>
                    <td>
                        {{ $waste->wastes_reference }}
                    </td>
                    <td>
                        {{ $waste->wastelists_name }}
                    </td>
                    <td>
                        {{ $waste->getCreated($waste->wastes_created_at)}}
                    </td>
                    <td>
                        <div class="table-button-holder">
                            <a href="{{ route('waste.new', ['id' => $waste->wastes_id ]) }}"class="ph-button ph-button-standard table-button">@include("icons.edit")</a>
                            <a href="{{ route('waste.summary', [$waste->wastes_id ]) }}"class="ph-button ph-button-standard table-button">@include("icons.summary")</a>

                            <form method="POST" action="{{ route("waste.destroy", ["id" => $waste->wastes_id]) }}" >
                                @csrf
                                @method('delete')
                                <button class="ph-button ph-button-important table-button" type="submit">@include("icons.delete")</button>
                            </form>

                        </div>
                    </td>
                </tr>

            @endforeach
        </table>
        @endif
    </div>
    @endif
</div>
<x-tools.search-modal model="wastes" action='waste.view' search="{{ $search }}" :fields="$searchFields"></x-tools.search-modal>
@endsection
