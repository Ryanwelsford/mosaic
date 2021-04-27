<?php

namespace App\Http\Controllers\Types;

use App\Models\User;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminAccessController extends Controller
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

            if ($this->user->privelleges != "admin") {
                return redirect()->route('general.restricted');
            }

            //maybe update redirct to
            //Redirect::to('/login?attempt='. true) or something liek that
            return $next($request);
        });
    }
}
