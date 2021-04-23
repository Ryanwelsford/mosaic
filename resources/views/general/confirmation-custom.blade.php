@extends('layout')

@section('title', $title)

@section('content')

<div class="center-column">
    <div class ="confirmation-banner">
        <h1>{{ $heading }}</h1>
        <p>{{ $text }} </p>
        <p>Click <a class="anchor" href="{{ $anchor }}">here</a> {{ $anchorText }}</p>
    </div>
</div>
@endsection
