@props([
    'name',
    'value',
    'field' =>"id"
])

<input type="hidden" name="{{$name}}" value="@if(isset($value->$field)){{$value->$field}}@endif" >
