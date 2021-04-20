<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class UserAccessController extends Controller
{

    protected $user;

    public function __construct(Request $request)
    {

        //for whatever reason you cannot access the Auth facade within constructors, because that makes perfect sense...
        //therefore the below function can be used to access it
        //https://stackoverflow.com/questions/39175252/cant-call-authuser-on-controllers-constructor
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            //i.e not logged in
            if (is_null($this->user)) {

                return redirect("/login");
            }

            return $next($request);
        });
    }
}