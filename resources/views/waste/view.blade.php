@extends('layout')
@section('title', $title)

@section('tools')
    <button class="bar-tool-button" onclick="searchModal()"><span class="mobile-hidden">Search</span> <i class="fas fa-search"></i></button>
@endsection

@section('content')
<div class="grid-container">
<div class="main-tile tile-all-columns center-column">
    <h2>Store Details</h2>
    <form class="search-form grid-2-col-wide table-width-match" method="GET" action="{{ route("waste.view") }}">
        <label>Search Waste</label>
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

    @if($wastes->count() < 1 && !isset($search))
        <p>No counts currently exist, create a new waste <a href="{{ route("waste.new") }}">here</a></p>
    @endif

    @if(isset($search))
        <p>Displaying {{$wastes->count()}} results for... <span class="italics">{{ $search }}</span> @if(isset($sort)) {{ "sorted by ".$sort }} @endif</p>
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
                    {{ $waste->wastes_id }}
                </td>
                <td>
                    {{ $waste->wastelists_name }}
                </td>
                <td>
                    {{ $waste->getCreated($waste->wastes_created_at)}}
                </td>
                <td>
                    <div class="table-button-holder">
                        <a href="{{ route('waste.new', ['id' => $waste->wastes_id ]) }}"class="ph-button ph-button-standard table-button">Edit<img src="/images/icons/edit-48-black.png"></a>

                        <form method="POST" action="{{ route("waste.destroy", ["id" => $waste->wastes_id]) }}" >
                            @csrf
                            @method('delete')
                            <button class="ph-button ph-button-important table-button" type="submit">Delete <img src="/images/icons/delete-48-black.png"></button>
                        </form>

                    </div>
                </td>
            </tr>

        @endforeach
    </table>
    @endif
</div>
</div>
<x-tools.search-modal model="wastes" action='waste.view' search="{{ $search }}" :fields="$searchFields"></x-tools.search-modal>
@endsection
