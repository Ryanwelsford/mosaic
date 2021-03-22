@extends('layout')
@section('title', $title)
@section('content')
<div class="grid-container">
    <div class="main-tile">Test

        <div class="main-tile-button-container">

            <button class="ph-button ph-button-standard">Previous</button>
            <button class="ph-button ph-button-standard">Next</button>
        </div>
    </div>
    <div class="main-tile tile-2-4">Test
        <button class="ph-button ph-button-important">Click <img src="images/icons/book-48.png"></button>
    </div>
    <div class="main-tile">Test
        <button class="ph-button ph-button-important">Click <img src="images/icons/book-48.png"></button>
    </div>
    <div class="main-tile">Test
        <button class="ph-button ph-button-important">Click <img src="images/icons/book-48.png"></button>
    </div>
    <div class="main-tile">Test
        <button class="ph-button ph-button-important">Click <img src="images/icons/book-48.png"></button>
    </div>
    <div class="main-tile tile-1-3 center-column">
        <label class="select-label">Select a Category</label>
        <select class="main-select" placeholder="Select from the list">
            <option>1</option>
            <option>avocado</option>
            <option>1</option>
            <option>1</option>
        </select>
        <select class="main-select" placeholder="Select from the list">
            <option>1</option>
            <option>avocado</option>
            <option>1</option>
            <option>1</option>
        </select>
    </div>
    <div class="main-tile">Test</div>
    <div class="main-tile tile-all-columns">
        <div class="center-column">
            <h2>Table Heading</h2>
            <table>
                <tr>
                    <th>Test</th>
                    <th>Test</th>
                    <th>Test</th>
                    <th class="mob-hidden">Non-vital data</th>
                    <th>Test</th>
                </tr>
                <tr>
                    <td>Test</td>
                    <td>Test</td>
                    <td>Test</td>
                    <td class="mob-hidden">Test</td>
                    <td>Test</td>
                </tr>
                <tr>
                    <td>Lorem ipsum dolor sit amet.</td>
                    <td>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quasi tempora quia, aperiam suscipit aut impedit velit qui expedita ratione vero iure unde dolor! Nobis quisquam hic similique. Culpa, illo iste atque accusantium animi soluta. Incidunt, sequi pariatur rerum nisi animi fuga assumenda vel sed necessitatibus repellendus maiores a neque nostrum.</td>
                    <td>Test</td>
                    <td class="mob-hidden">Test</td>
                    <td>Test</td>
                </tr>
                <tr>
                    <td>Test</td>
                    <td>Test</td>
                    <td>Test</td>
                    <td class="mob-hidden">Test</td>
                    <td>Test</td>
                </tr>
            </table>
        </div>
    </div>
</div>
<section class="modal" id="mobile-modal-nav">
    <div class="modal-internal">
        <div class="modal-title">Dashboard <button onclick="OpenModalNav(event)" class="close-X">X</button></div>
        <div class="modal-content">
            <div class="grid-2-col">
                <button class="ph-button ph-button-standard">Inventory</button>
                <button class="ph-button ph-button-standard">Ordering</button>
                <button class="ph-button ph-button-standard">Recieving</button>
                <button class="ph-button ph-button-standard">Waste</button>
                <button class="ph-button ph-button-standard">Forecasting</button>
                <button class="ph-button ph-button-standard">SOH</button>
                <button class="ph-button ph-button-standard">Dates</button>
                <button class="ph-button ph-button-standard">User</button>
                <div class="center-button-container"><button class="ph-button ph-button-important" onclick="OpenModalNav(event)">Close</button></div>
            </div>
        </div>
    </div>
</section>
@endsection
