<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//unfinished controller the intention was to allow for pdf upload of shelf life chart, and creation of long term tracking of dates through a calendar concept
class DatesController extends Controller
{

    //provide a menu homepage for controller
    public function home()
    {

        $title = "Dates Home";

        //set title of tile, routing on click and image to be displayed based on menu-tile component.
        $menuitems = [
            ["title" => "New Date", "anchor" => "/test", "img" => "/images/icons/new-256.png"],
            ["title" => "Edit Date", "anchor" => "/test", "img" => "/images/icons/edit-256.png"],
            ["title" => "Search Dates", "anchor" => "/test", "img" => "/images/icons/search-256.png"],
            ["title" => "View Shelf Life Chart", "anchor" => "/test", "img" => "/images/icons/view-256.png"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
        ]);
    }
}
