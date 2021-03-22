<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function home() {

        $title = "Order Home";

        $menuitems = [
            ["title" => "New Order", "anchor" => "/test", "img" => "/images/icons/new-256.png"],
            ["title" => "Edit Order", "anchor" => "/test", "img" => "/images/icons/edit-256.png"],
            ["title" => "View Order", "anchor" => "/test", "img" => "/images/icons/view-256.png"],
            ["title" => "Order Reports", "anchor" => "/test", "img" => "/images/icons/report-256.png"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
            ]);
    }
}
