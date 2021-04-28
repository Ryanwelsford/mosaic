<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function restricted()
    {
        $title = "Access Restricted";
        $heading = "Access is restriced based on user permissions";
        $text = "Contact system administrator for further details";
        return view("general.restricted", ["title" => $title, "heading" => $heading, "text" => $text]);
    }

    public function welcome()
    {

        if(is_null(auth()->user())) {
            return redirect()->route('logout.index');
        }

        $title = "Welcome to Mosaic";
        $heading = "You are logged in ";
        $text = "Access user functions using the sidebar or mobile navigation";
        return view("general.restricted", ["title" => $title, "heading" => $heading, "text" => $text]);
    }
}
