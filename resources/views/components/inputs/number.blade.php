@props([
    "inputName" ,
    "error" => $inputName,
    "value" ,
    "customMessage" => null,
    "min" => "0",
    "max" => "1000",
    "step" => "1"
    ])
    <!--setting a variable with => sets the default as well, to just pass in the value just pass the name in do not assign it-->

<!--So you can pass in a custom message if the default error message from laravel isnt enough as customMessage-->
<div>
    <input name="{{ $inputName }}" value="@if(isset($value->$inputName)){{$value->$inputName}}@endif" class="@error($error) input-error @enderror" type="number" min={{$min}} max={{$max}} step={{$step}}>
    @error($error)
        <div class="small-error-text error-text">
            @if(is_null($customMessage))
                {{ $message }}
            @else
                {{ $customMessage }}
            @endif
        </div>
    @enderror
</div>
