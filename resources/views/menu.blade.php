@extends('layout')

@section('title', $title)

@section('content')

<div class="grid-container-2">

    @foreach($menuitems as $item)
        <x-tile-menu title="{{ $item['title'] }}" anchor="{{ $item['anchor'] }}" img="{{ $item['img'] }}"></x-tile-menu>
    @endforeach

</div>
@endsection
