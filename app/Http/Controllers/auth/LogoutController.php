<?php

namespace App\Http\Controllers\auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


/********************************************************
 *Controller allows users to logout of system cleanly, routing back to the login page with a logout message
 ********************************************************/
class LogoutController extends Controller
{

    //ensure only logged in users can access the pages within the logout controller
    public function __construct()
    {
        $this->middleware('auth');
    }

    //logout of system, reset sessions, remove csrf tokens ensure logged out of all devices.
    public function index()
    {

        Auth::logout();

        //need to logout users from all devices, and clear csrf tokens

        return redirect()->route("login")->with("logout", "You have successfully logged out");
    }
}
