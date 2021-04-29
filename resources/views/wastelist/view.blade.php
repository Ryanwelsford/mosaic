@extends('layout')
@section('title', $title)

@section("tools")
    <button class="bar-tool-button" onclick="searchModal()"><span class="mobile-hidden">Search</span> <i class="fas fa-search"></i></button>
@endsection

@section('content')
<div class="grid-container">
    <div class="main-tile tile-all-columns center-column">
        <form class="search-form grid-2-col-wide table-width-match" method="GET" action="{{ route("wastelist.view") }}">
            <label>Search Lists</label>
            <div class="search-with-button">
                <input name="search" type ="text" class=""  id="search-bar" placeholder="Search here" value="@if(isset($search)){{$search}}@endif">
                <button type ="submit" class="ph-button ph-button-standard">Search</button>
            </div>
        </form>

        @if($wastelists->count() < 1 && !isset($search))
            <p>No lists currently exist, create a new list <a href="{{ route("wastelist.new") }}">here</a></p>
        @endif

        @if(isset($search))
            <p>Displaying {{$wastelists->count()}} of {{ $wastelists->total() }} results for... <span class="italics">{{ $search }}</span> @if(isset($sort)) {{ "sorted by ".$sort }} @endif</p>
        @endif
    </div>

    @if($wastelists->count() >= 1)
    <div class="main-tile tile-all-columns center-column">
        <h2>Waste List Details</h2>

        @if(isset($response) && $response != '')
            <x-confirmation-message :message="$response"></x-confirmation-message>
        @endif

        <table class="wide-table full-width reduced-table">
            <th>
                Name
            </th>
            <th>
                Description
            </th>
            <th class="mob-hidden">
                Status
            </th>
            <th class="mob-hidden">
                Created at
            </th>
            <th>
                Options
            </th>

            @foreach($wastelists as $key => $wastelist)
                <tr>
                    <td>
                        {{ $wastelist->name }}
                    </td>
                    <td>
                        {{ $wastelist->description }}
                    </td>
                    <td class="mob-hidden">
                        {{ $wastelist->status }}
                    </td>
                    <td class="mob-hidden">
                        {{ $wastelist->created_at->format('d M Y') }}
                    </td>
                    <td>
                        <div class="table-button-holder">
                            <a href="{{ route('wastelist.new', ['id' => $wastelist->id]) }}"class="ph-button ph-button-standard table-button">@include('icons.edit')</a>
                            <form method="POST" action="{{ route("wastelist.destroy", $wastelist) }}">
                                <button class="ph-button ph-button-important table-button" type="submit">@include('icons.delete')</button>
                                @csrf
                                @method('delete')
                            </form>
                        </div>
                    </td>
                </tr>

            @endforeach
        </table>

        {{ $wastelists->links('paginate.default', ["paginator" => $wastelists, "search" => $search, "sort" => $sort]) }}
    </div>
    @endif
</div>
<x-tools.search-modal model="Waste Lists" action='wastelist.view' search="{{ $search }}" :fields="$searchFields"></x-tools.search-modal>
@endsection
