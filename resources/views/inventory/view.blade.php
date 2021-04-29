@extends('layout')
@section('title', $title)

@section("tools")
    <button class="bar-tool-button" onclick="searchModal()"><span class="mobile-hidden">Search</span> <i class="fas fa-search"></i></button>
@endsection

@section('content')
<div class="grid-container">
    <div class="main-tile tile-all-columns center-column">
        <form class="search-form grid-2-col-wide table-width-match" method="GET" action="{{ route("inventory.view") }}">
            <label>Search Counts</label>
            <div class="search-with-button">
                <input name="search" type ="text" class=""  id="search-bar" placeholder="Search here" value="@if(isset($search)){{$search}}@endif">
                <button type ="submit" class="ph-button ph-button-standard">Search</button>
            </div>
        </form>

        @if(isset($search))
            <p>Displaying {{$inventory->count()}} results for... <span class="italics">{{ $search }}</span> @if(isset($sort)) {{ "sorted by ".$sort }} @endif</p>
        @endif

        @if($inventory->count() < 1 && !isset($search))
            <p>No receipts currently exist, create a new receipt <a href="{{ route("inventory.new") }}">here</a></p>
        @endif
    </div>

    @if($inventory->count() >= 1)
    <div class="main-tile tile-all-columns center-column">
        <h2>Count Details</h2>

        @if(isset($inventory) && $response != '')
            <div class ="confirmation-banner confirmation-message margin-bottom-2 full-width">
                <h3>{{ $response }} <button onclick="closeDiv(event)" class="close-X">X</button></h3>
            </div>
        @endif



        @if($inventory->count() >= 1)
        <table class="wide-table full-width reduced-table">
            <th class="mob-hidden">
                Count #
            </th>
            <th class="">
                Created On
            </th>
            <th class="">
                Status
            </th>
            <th>
                Options
            </th>

            @foreach($inventory as $each)
                <tr>
                    <td class="mob-hidden">{{ $each->id }}</td>
                    <td>{{ $each->created_at->format('d M Y') }}</td>
                    <td>{{ $each->status }}</td>
                    <td>
                        <div class="table-button-holder">
                            <a href="{{ route('inventory.summary', $each->id) }}"class="ph-button ph-button-standard table-button">Summary <i class="fas fa-clipboard-list"></i></a>
                            @if($each->status == "Saved")
                                <a href="{{ route('inventory.new', ['id' => $each->id]) }}"class="ph-button ph-button-standard table-button">Edit</a>

                                <form method="POST" action="{{ route("inventory.destroy", $each) }}" class="table-button">
                                    <button class="ph-button ph-button-standard ph-button-important table-button" type="submit">Delete</button>
                                    @csrf
                                    @method('delete')
                                </form>
                            @endif


                        </div>
                    </td>
                </tr>

            @endforeach
        </table>
        @endif
        {{ $inventory->links('paginate.default', ["paginator" => $inventory, "search" => $search, "sort" => $sort]) }}
    </div>
    @endif

    <x-tools.search-modal model="Inventory" action='inventory.view' search="{{ $search }}" :fields="$searchFields"></x-tools.search-modal>

</div>


@endsection
