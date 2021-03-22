<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WasteController extends Controller
{
    public function home() {

        $title = "Waste Home";

        $menuitems = [
            ["title" => "New Waste", "anchor" => "/test", "img" => "/images/icons/new-256.png"],
            ["title" => "Edit Waste", "anchor" => "/test", "img" => "/images/icons/edit-256.png"],
            ["title" => "View Waste", "anchor" => "/test", "img" => "/images/icons/view-256.png"],
            ["title" => "Waste Reports", "anchor" => "/test", "img" => "/images/icons/report-256.png"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
            ]);
    }
}
