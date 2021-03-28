@props(["message" => $message, "action" => "closeDiv(event)"])

<div class ="confirmation-banner confirmation-message">
    <p> {{ $message }} <button onclick="{{$action}}" class="close-X">X</button></p>
</div>
