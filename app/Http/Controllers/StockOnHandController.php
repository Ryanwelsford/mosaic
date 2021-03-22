<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StockOnHandController extends Controller
{
    public function home() {

        $title = "Stock on Hand Home";

        $menuitems = [
            ["title" => "New Stock on Hand Count", "anchor" => "/test", "img" => "/images/icons/new-256.png"],
            ["title" => "Edit Stock on Hand Count", "anchor" => "/test", "img" => "/images/icons/edit-256.png"],
            ["title" => "View Stock on Hand", "anchor" => "/test", "img" => "/images/icons/view-256.png"],
            ["title" => "Stock on Hand Reports", "anchor" => "/test", "img" => "/images/icons/report-256.png"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
            ]);
    }
}
