@props(['title' => $title, 'anchor' => $anchor, 'img' => $img, "action" => "Select"])


<div class="main-tile center-column">
    <img class="menu-icon" src="{{ $img }}">
    <h2>{{ $title }}</h2>
    <a class="auto-top" href="{{ $anchor }}"><button class="ph-button ph-button-standard ph-button-large">{{ ucwords($action) }}</button></a>
</div>
