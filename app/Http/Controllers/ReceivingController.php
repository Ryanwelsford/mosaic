<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReceivingController extends Controller
{
    public function home() {

        $title = "Receiving Home";

        $menuitems = [
            ["title" => "New Receipt", "anchor" => "/test", "img" => "/images/icons/new-256.png"],
            ["title" => "Edit receipt", "anchor" => "/test", "img" => "/images/icons/edit-256.png"],
            ["title" => "View receipt", "anchor" => "/test", "img" => "/images/icons/view-256.png"],
            ["title" => "Receipt Reports", "anchor" => "/test", "img" => "/images/icons/report-256.png"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
            ]);
    }
}
