@extends('layout')
@section('title', $title)

@section("tools")
    <button class="bar-tool-button" onclick="searchModal()"><span class="mobile-hidden">Search</span> <i class="fas fa-search"></i></button>
@endsection

@section('content')
<div class="grid-container">
    <div class="main-tile tile-all-columns center-column">
        <h2>Admin Details</h2>
        <form class="search-form grid-2-col-wide table-width-match" method="GET" action="{{ route("admin.view") }}">
            <label>Search Admins</label>
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

        @if($admins->count() < 1 && !isset($search))
            <p>No admins currently exist, create a new admin <a href="{{ route("admin.new") }}">here</a></p>
        @endif

        @if(isset($search))
            <p>Displaying {{$admins->count()}} results for... <span class="italics">{{ $search }}</span> @if(isset($sort)) {{ "sorted by ".$sort }} @endif</p>
        @endif

        @if($admins->count() >= 1)
        <table class="wide-table full-width reduced-table">
            <th class="mob-hidden">
                Admin #
            </th>
            <th class="">
                Name
            </th>
            <th class="">
                Email
            </th>
            <th>
                Options
            </th>

            @foreach($admins as $admin)
                <tr>
                    <td class="mob-hidden">
                        {{ $admin->id }}
                    </td>

                    <td class="">
                        {{ $admin->name }}
                    </td>

                    <td class="">
                        {{ $admin->email }}
                    </td>

                    <td>
                        <div class="table-button-holder">
                            <a href="{{ route('admin.new', ['id' => $admin->id]) }}"class="ph-button ph-button-standard table-button">Edit</a>

                            <form method="POST" action="{{ route("admin.destroy", $admin) }}" class="table-button">
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


    <x-tools.search-modal model="Admins" action='admin.view' search="{{ $search }}" :fields="$searchFields"></x-tools.search-modal>

</div>
<x-top-button></x-top-button>

@endsection
