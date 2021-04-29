@props(["message" => $message, "action" => "closeDiv(event)"])

@if(isset($message) && $message != '')
<div class ="confirmation-banner confirmation-message margin-bottom-2 full-width">
    <h3>{{ $message }} <button onclick="closeDiv(event)" class="close-X">X</button></h3>
</div>
@endif
