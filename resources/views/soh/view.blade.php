@extends('layout')
@section('title', $title)

@section("tools")
    <button class="bar-tool-button" onclick="searchModal()"><span class="mobile-hidden">Search</span> <i class="fas fa-search"></i></button>
@endsection

@section('content')
<div class="grid-container">
    <div class="main-tile tile-all-columns center-column">
        <x-search-form model="Stock On Hand" action="{{ route('soh.view') }}" :search="$search"></x-search-form>

        @if($sohs->count() < 1 && !isset($search))
            <p>No counts currently exist, create a new count <a href="{{ route("soh.new") }}">here</a></p>
        @endif

        @if(isset($search))
            <p>Displaying {{$sohs->count()}} results for... <span class="italics">{{ $search }}</span> @if(isset($sort)) {{ "sorted by ".$sort }} @endif</p>
        @endif

    </div>

    @if($sohs->count() >= 1)
    <div class="main-tile tile-all-columns center-column">
        <h2>Stock on Hand Details</h2>

        <x-confirmation-message :message="$response"></x-confirmation-message>


        @if($sohs->count() >= 1)
        <table class="wide-table full-width reduced-table">
            <th class="mob-hidden">
                Count #
            </th>
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
                    <td class="mob-hidden">
                        {{ $soh->id }}
                    </td>
                    <td class="">
                        {{ $soh->reference }}
                    </td>

                    <td class="">
                        {{ $soh->created_at->format('d M Y') }}
                    </td>

                    <td>
                        <div class="table-button-holder">
                            <a href="{{ route('soh.new', ['id' => $soh->id]) }}"class="ph-button ph-button-standard table-button">@include("icons.edit")</a>
                            <form method="POST" action="{{ route("soh.destroy", $soh) }}" class="table-button">
                                <button class="ph-button ph-button-standard ph-button-important table-button" type="submit">@include('icons.delete')</button>
                                @csrf
                                @method('delete')
                            </form>


                        </div>

                    </td>
                </tr>

            @endforeach
        </table>
        @endif
        {{ $sohs->links('paginate.default', ["paginator" => $sohs, "search" => $search, "sort" => $sort]) }}
    </div>
    @endif

    <x-tools.search-modal model="stock on hand" action='soh.view' search="{{ $search }}" :fields="$searchFields"></x-tools.search-modal>

</div>

@endsection
