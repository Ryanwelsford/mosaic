@extends('layout')
@section('title', $title)

@section('content')
<div class="grid-container">
    <div class="main-tile tile-all-columns center-column">
        <label class="select-label">Select a Category</label>
        <select id="category" class="main-select main-select-large" placeholder="Select from the list">
            @foreach($categories as $key=>$category)
            <option value= "{{ $key }}">{{ $key }}</option>
            @endforeach
        </select>
        <select class="main-select main-select-large" placeholder="Select from the list">
            @foreach($categories['Chilled'] as $option)
                <option value= "{{ $option }}">{{ $option }}</option>
            @endforeach
        </select>
    </div>
    <div class="main-tile tile-all-columns center-column">
        <h2>Product Details</h2>
        @if(session("confirmation"))
            <p>{{ session("confirmation") }}</p>
        @endif
        <table class="wide-table">
            <th>
                <p class="mob-hidden">Product Name</p>
                <p class="mobile-only">Details</p>
            </th>
            <th class="mob-hidden">
                Supplier Code
            </th>
            <th class="mob-hidden">
                Category
            </th>
            <th class="mob-hidden">
                Subcategory
            </th>
            <th>
                Options
            </th>

            @foreach($products as $product)
                <tr>
                    <td>
                        {{ $product->name }}
                        <p class='mobile-only'>{{ $product->code }}</p>
                    </td>

                    <td class="mob-hidden">
                        {{ $product->code }}
                    </td>

                    <td class="mob-hidden">
                        {{ $product->category }}
                    </td>

                    <td class="mob-hidden">
                        {{ $product->subcategory }}
                    </td>

                    <td>
                        <div class="table-button-holder">
                            <a href="{{ route('product.new', ['id' => $product->id]) }}"class="ph-button ph-button-standard table-button">Edit<img src="/images/icons/edit-48-black.png"></a>
                            <form method="POST" action="{{ route("product.destroy", $product) }}">
                                <button class="ph-button ph-button-important table-button" type="submit">Delete</button>
                                @csrf
                                @method('delete')
                            </form>
                        </div>

                    </td>
                </tr>

            @endforeach
        </table>
    </div>

</div>
<x-top-button></x-top-button>
<script>
    let main = document.getElementById("category");
    main.onchange = function(event) {
        redrawTable();
    }

    function redrawTable() {
        removeTag("tr", 1);
        addTableTr();
    }

    function removeTag(tag, countToLeave = 1) {
        //get all elements by their tag
        elements = document.getElementsByTagName(tag);

        //remove all elements from list end, leaving count behind.
        if(elements.length > countToLeave) {
            for(i = elements.length ; elements.length > countToLeave; i--) {
                elements[i-1].remove();
            }
        }
    }
</script>
@endsection
