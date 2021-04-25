@extends('layout')
@section('title', $title)
@section("tools")
<button class="bar-tool-button" type="button" onclick="openOrCloseModal('search-modal')" name="save" value="save">Add <i class="fas fa-plus-circle"></i></button>
<button form="form" name="book" type="submit" class="bar-tool-button" value="book"><span class="mobile-hidden">Book</span> <i class="fas fa-book"></i></button>
@endsection
@section('content')


<div class="grid-container">
    <div class="main-tile tile-all-columns center-column">
        <h2>New Waste Entry</h2>
        <form method="POST" action="{{ route("waste.new") }}" id="form" class="center-column full-width">
            @csrf
            <input type="hidden" name="id" value="@if(isset($wastes->id)){{$wastes->id}}@endif">
            <div class="right-content full-width margin-bottom-2">
                <h3>Add Products to Waste </h3>
                <button type="button" class="ph-button ph-button-standard margin-left-2" onclick="openOrCloseModal('search-modal')" name="save" value="save">Add <i class="fas fa-plus-circle"></i></button>
            </div>
            <div class="grid-2-col-wide centered">
                <label>Reference: @error('reference') <span class="error-text">*</span> @enderror</label>
                <div>
                    <input name="reference" class="@error('reference') input-error @enderror"type="text" value="@if(isset($wastes->reference)){{ $wastes->reference }} @endif" placeholder="Enter a reference">
                    @error('reference')
                            <div class="small-error-text error-text">{{ $message }} </div>
                    @enderror
                </div>
                <label>Waste Type:</label>
                <select name="wastelist_id">
                    @foreach($wastelists as $key => $option)
                        <option value="{{$option->id}}"@if(isset($wastes->wastelist_id) && $wastes->wastelist_id == $option->id) {{ "selected" }} @endif>{{ ucwords($option->name) }}</option>
                    @endforeach
                </select>
            </div>
            <table class="wide-table full-width reduced-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th class="mob-hidden">Code</th>
                        <th>Case</th>
                        <th>Price</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            @error('product')
            <div class="small-error-text error-text margin-bottom-2">{{ $message }} </div>
            @enderror
        </form>
        </form>
        <div class="tile-all-columns center-column margin-top">
                <button form="form" type="submit" class="ph-button ph-button-standard">Book Waste</button>
        </div>
    </div>
</div>

<x-tools.add-product :categories="$categories"></x-tools.add-product>
@endsection
