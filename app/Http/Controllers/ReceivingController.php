<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Types\UserAccessController;

class ReceivingController extends UserAccessController
{
    public function home()
    {

        $title = "Receiving Home";

        $menuitems = [
            ["title" => "New Receipt", "anchor" => "/test", "img" => "/images/icons/new-256.png"],
            ["title" => "Edit receipt", "anchor" => "/test", "img" => "/images/icons/edit-256.png"],
            ["title" => "View receipts", "anchor" => "/test", "img" => "/images/icons/view-256.png"],
            ["title" => "Receipt Reports", "anchor" => "/test", "img" => "/images/icons/report-256.png"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
        ]);
    }

    //TODO
    //select order for reciept to be based on
    public function store()
    {
    }
    //save a reciept to db
    public function save()
    {
        //add a guard to prevent incorrect reciept id or something liek that
    }
    //display list of created reciepts with view,delete, print options
    public function view()
    {
    }

    //single reciept summary in printable
    public function print()
    {
    }

    //view a single reciept summary
    public function viewReciept()
    {
    }
    //delete reciept
    public function delete()
    {
    }
}
