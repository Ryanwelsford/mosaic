@extends('layout')
@section('title', $title)
@section('tools')
<button class="bar-tool-button" onclick="openOrCloseModal('search-modal')" name="save" value="save"><span class="mobile-hidden">Info</span> <i class="far fa-question-circle"></i></button>
@endsection
@section('content')
<div class="grid-container">
    <div class="main-tile">
        <table class="wide-table full-width reduced-table" id="findable">
            <thead>
                <th>Code</th>
                <th>Description</th>
                <th>Units</th>
                <th>Count</th>
                <th>Total</th>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <input id="box" data-count="5" type="number" class="table-input" min="0" step="1" value="0">
                    </td>
                    <td>
                        <input type="number" class="table-input total-box" min="0" step="1" value="0">
                    </td>
                </tr>

                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <input type="number" class="table-input" min="0" step="1" value="0">
                    </td>
                    <td>
                        <input type="number" class="table-input total-box" min="0" step="1" value="0">
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
</div>

<script>
    //actual addition function
    function totalUp() {
        console.log("focusout");
    }

    //add event listeners
    function setupTotals() {
        let input = document.getElementById("box");

        input.addEventListener('focusout', (event) => {
            let current = parseFloat(event.target.value);
            let tr = event.target.parentNode.parentNode;
            let totalBox = tr.getElementsByClassName("total-box")[0];
            let runningTotal = parseFloat(totalBox.value);


            //data attributes are set using data-name, but accessed in js using dataset.name
            let newTotal = runningTotal + (current*parseFloat(event.target.dataset.count)) ;

            totalBox.value = newTotal;
            console.dir(newTotal);
            event.target.value = 0;
        });

    }

    setupTotals();
</script>
@endsection
