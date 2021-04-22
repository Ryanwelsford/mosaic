@extends('layout')
@section('title', $title)

@section('content')

<div class="grid-container">
    <div class="main-tile tile-all-columns center-column">
            <h2 class="tile-title tile-all-columns ">Select Order</h2>

            <table class="wide-table full-width reduced-table">
                <tr>
                    <th class="mob-hidden">
                        Order #
                    </th>
                    <th class="">
                        Reference
                    </th>
                    <th class="">
                        Status
                    </th>
                    <th class="">
                        Created on
                    </th>
                    <th class="">
                        Delivery
                    </th>
                    <th>
                        Options
                    </th>
                </tr>
                @foreach($orders as $order)
                    <tr>
                        <td class="mob-hidden">
                            {{ $order->id }}
                        </td>

                        <td class="">
                            {{ $order->reference }}
                        </td>

                        <td class="">
                            {{ $order->status }}
                        </td>

                        <td class="">
                            {{ $order->created_at->format('d M Y') }}
                        </td>

                        <td class="">
                            {{ $order->getDeliveryDate()->format('d M Y') }}
                        </td>

                        <td>
                            <div class="table-button-holder">

                                <form method="POST" action="{{ route("receiving.assign", [$order]) }}" class="table-button">
                                    <button class="ph-button ph-button-standard table-button" type="submit">Select <i class="far fa-hand-pointer"></i></button>
                                    @csrf
                                </form>

                                <a target="_blank" href="{{ route('order.summary', ['id' => $order->id]) }}"class="ph-button ph-button-standard table-button">Summary <i class="fas fa-clipboard-list"></i></a>

                            </div>

                        </td>
                    </tr>

                @endforeach
            </table>

        <p class="right-aligned">Looking to edit a reciept click <a href="{{route('receiving.edit')}}">here</a></p>
    </div>
</div>

@endsection
