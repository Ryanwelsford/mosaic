@extends('layout')
@section('title', $title)

@section('content')
<div class="grid-container">
<div class="main-tile tile-all-columns center-column">
    <h2>Waste List Details</h2>
    <table class="wide-table">
        <th>
            <p class="mob-hidden">Name</p>
            <p class="mobile-only">Details</p>
        </th>
        <th class="mob-hidden">
            Description
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
                    <p class='mobile-only'>{{ $wastelist->description }} {{ $wastelist->created_at->format('D M Y') }}</p>
                </td>
                <td class="mob-hidden">
                    {{ $wastelist->description }}
                </td>
                <td class="mob-hidden">
                    {{ $wastelist->created_at->format('D M Y') }}
                </td>
                <td>
                    <div class="table-button-holder">
                        <a href="{{ route('wastelist.new', ['id' => $wastelist->id]) }}"class="ph-button ph-button-standard table-button">Edit<img src="/images/icons/edit-48-black.png"></a>
                        <form method="POST" action="{{ route("wastelist.destroy", $wastelist) }}">
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
<x-top-button></x-top-button>
@endsection
