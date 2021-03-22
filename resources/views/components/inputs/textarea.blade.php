@props([
    "inputName" ,
    "error" => $inputName,
    "value" ,
    "customMessage" => null
    ])

<section>
    <textarea
    name="{{$inputName}}"
    class="@error($error) input-error @enderror">@if(isset($value->$inputName)){{$value->$inputName}}@endif</textarea>

    @error('description')
            <div class="small-error-text error-text">{{ $message }} </div>
    @enderror
</section>
