<?php

namespace App\Http\Controllers\auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {

        Auth::logout();

        //need to logout users from all devices, and clear csrf tokens

        return redirect()->route("login")->with("logout", "You have successfully logged out");
    }
}
