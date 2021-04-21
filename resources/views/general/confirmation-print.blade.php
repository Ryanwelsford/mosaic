@extends('layout')

@section('title', $title)

@section('content')

<div class="center-column">
    <div class ="confirmation-banner">
        <h1>{{ $heading }}</h1>
        <p>{{ $text }} </p>
        <p>Click <a class="anchor" target="_blank" href="{{ $anchor }}">here</a> to print the document</p>
    </div>
</div>
@endsection
