<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//very short controller based on very few required functions, simply display short messages on login entry

class GeneralController extends Controller
{
    //displayed when incorrect user type attempts access to non-user type area
    public function restricted()
    {
        $title = "Access Restricted";
        $heading = "Access is restriced based on user permissions";
        $text = "Contact system administrator for further details";
        return view("general.restricted", ["title" => $title, "heading" => $heading, "text" => $text]);
    }

    //displayed if user is logged in while attempting to reach login page
    public function welcome()
    {

        if (is_null(auth()->user())) {
            return redirect()->route('logout.index');
        }

        $title = "Welcome to Mosaic";
        $heading = "You are logged in ";
        $text = "Access user functions using the sidebar or mobile navigation";
        return view("general.restricted", ["title" => $title, "heading" => $heading, "text" => $text]);
    }
}
