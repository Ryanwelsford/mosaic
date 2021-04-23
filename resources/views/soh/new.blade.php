@extends('layout')
@section('title', $title)
@section('tools')
<button class="bar-tool-button" onclick="openOrCloseModal('search-modal')" name="save" value="save"><span class="mobile-hidden">Info</span> <i class="far fa-question-circle"></i></button>
<button type="submit" form="form" class="bar-tool-button" onclick="openOrCloseModal('search-modal')" name="save" value="save"> @include("icons.book") </button>
<button class="bar-tool-button" onclick="openOrCloseModal('search-modal')" name="save" value="save"><span class="mobile-hidden">Find</span> <i class="fas fa-search-location"></i></button>
@endsection
@section('content')
<div class="grid-container">
    <form class="main-tile" id="form" action="{{ route('soh.new') }}" method="POST">
        @csrf
        <h2 class="tile-title tile-all-columns ">Stock on Hand Count</h2>
        <table class="wide-table full-width reduced-table" id="findable">
            <thead>
                <th class="mob-hidden">Code</th>
                <th>Description</th>
                <th>Category</th>
                <th>Count</th>
                <th>Total</th>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr>
                        <td class="mob-hidden">{{ $product->code }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category }} - {{ $product->subcategory }}</td>
                        <td>
                            <div class="counter-holder">
                                <input  data-count="{{ $product->units->quantity }}" type="number" class="table-input count-box" min="0" step="1" placeholder="Case: {{ $product->units->description }}">

                                @if($product->units->pack != "none")
                                    <input data-count="{{ $product->units->packQuantity() }}" type="number" class="table-input count-box" min="0" step="1" placeholder="Pack: {{ $product->units->pack_description }}">
                                @endif

                                <input id="box" data-count="1" type="number" class="table-input count-box" min="0" step="1" placeholder="Each: 1">
                            </div>
                        </td>
                        <td class="total-td">
                            <input name="product[{{$product->id}}]" type="number" class="table-input total-box" min="0" step="1" value="0">
                        </td >
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="tile-all-columns center-column margin-top">
            <button form="form" name="save" value="save" type="submit" class="ph-button ph-button-standard ph-button-important">@include("icons.book")</button>
        </div>
    </form>
</div>

<x-tools.find-modal model="products"></x-tools.find-modal>

<script>

</script>
@endsection
