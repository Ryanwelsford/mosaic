@props([
    "attributes",
    "inputName" ,
    "error" => $inputName,
    "value" ,
    "customMessage" => null,
    "type" => "text",
    "class" => "",
    "errorRequired" => true
    ])
    <!--setting a variable with => sets the default as well, to just pass in the value just pass the name in do not assign it-->

<!--So you can pass in a custom message if the default error message from laravel isnt enough as customMessage-->
<div>
    <input name="{{ $inputName }}" class=" {{ $class }} @error($error) input-error @enderror"type="{{ $type }}"
    value="@if(isset($value->$inputName)){{$value->$inputName}} @endif">

    @if($errorRequired)

    @error($error)
    <div class="small-error-text error-text">
        @if(is_null($customMessage))
            {{ $message }}
        @else
            {{ $customMessage }}
        @endif
    </div>
    @enderror

    @endif
</div>
