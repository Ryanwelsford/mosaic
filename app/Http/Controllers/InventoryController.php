<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function home() {

        $title = "Inventory Home";

        $menuitems = [
            ["title" => "New Count", "anchor" => "/test", "img" => "/images/icons/new-256.png"],
            ["title" => "Edit Count", "anchor" => "/test", "img" => "/images/icons/edit-256.png"],
            ["title" => "View Count", "anchor" => "/test", "img" => "/images/icons/view-256.png"],
            ["title" => "Inventory Reports", "anchor" => "/test", "img" => "/images/icons/report-256.png"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
            ]);
    }
}
