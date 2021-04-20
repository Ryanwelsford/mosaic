<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Helpers\ModelValidator;


class OrderController extends UserAccessController
{
    //$user is available as a protected variable of UseraAccessController

    public function home()
    {

        $title = "Order Home";
        $menuitems = [
            ["title" => "New Order", "anchor" => "/test", "img" => "/images/icons/new-256.png"],
            ["title" => "Edit Saved Order", "anchor" => "/test", "img" => "/images/icons/edit-256.png"],
            ["title" => "View Orders", "anchor" => "/test", "img" => "/images/icons/view-256.png"],
            ["title" => "Order Reports", "anchor" => "/test", "img" => "/images/icons/report-256.png"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
        ]);
    }

    public function store(Request $request)
    {
        $title = "Create An Order";

        $order = false;

        return view("orders.new", [
            "title" => $title,
            "order" => $order
        ]);
    }
}
