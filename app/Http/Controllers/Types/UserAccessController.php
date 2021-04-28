<?php

namespace App\Http\Controllers\Types;

use App\Models\User;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserAccessController extends Controller
{

    protected $user;
    protected $store;

    public function __construct()
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

            if ($this->user->privelleges != "store") {
                return redirect()->route('general.restricted');
            }

            $this->store = Store::where('user_id', $this->user->id)->get()->first();
            //maybe update redirct to
            //Redirect::to('/login?attempt='. true) or something liek that
            return $next($request);
        });
    }
}
