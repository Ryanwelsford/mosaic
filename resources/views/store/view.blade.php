@extends('layout')
@section('title', $title)

@section('content')
<div class="grid-container">
<div class="main-tile tile-all-columns center-column">
    <h2>Store Details</h2>
    <table class="wide-table">
        <th>
            <p class="mob-hidden">Name</p>
            <p class="mobile-only">Details</p>
        </th>
        <th class="mob-hidden">
            Email
        </th>
        <th class="mob-hidden">
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
                    {{ $store->name }}
                    <p class='mobile-only'>{{ $store->users->name }}</p>
                </td>
                <td class="mob-hidden">
                    {{ $store->users->email }}
                </td>
                <td class="mob-hidden">
                    {{ $store->number }}
                </td>
                <td class="mob-hidden">
                    {{ $store->created_at->format('D M Y') }}
                </td>
                <td>
                    <div class="table-button-holder">
                        <a href="{{ route('store.new', ['id' => $store->id]) }}"class="ph-button ph-button-standard table-button">Edit<img src="/images/icons/edit-48-black.png"></a>
                        <form method="POST" action="{{ route("store.destroy", $store) }}">
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
