@props(['title' => $title, 'anchor' => $anchor, 'img' => $img])


<div class="main-tile center-column">
    <img class="menu-icon" src="{{ $img }}">
    <h2>{{ $title }}</h2>
    <a class="auto-top" href="{{ $anchor }}"><button class="ph-button ph-button-standard ph-button-large">Select</button></a>
</div>
