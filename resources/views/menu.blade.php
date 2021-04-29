@extends('layout')

@section('title', $title)

@section('content')

<div class="grid-container-2">

    @foreach($menuitems as $item)
    @if(isset($item['action']))
        <x-tile-menu
            title="{{ $item['title'] }}"
            anchor="{{ $item['anchor'] }}"
            img="{{ $item['img'] }}"
            action="{{ $item['action'] }}">
        </x-tile-menu>
    @else
        <x-tile-menu
            title="{{ $item['title'] }}"
            anchor="{{ $item['anchor'] }}"
            img="{{ $item['img'] }}">
        </x-tile-menu>
    @endif

    @endforeach

</div>
@endsection
