<?php

namespace App\Http\Controllers\auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/********************************************************
 *Controller is repsonsible for ensuring a user can login and authorising users to the correct account
 *
 ********************************************************/

class LoginController extends Controller
{
    //constructor ensures that only non-logged in users can use the login controller, therefore if you are already logged in you cannot reach the login page
    public function __construct()
    {
        $this->middleware('guest');
    }

    //display login page
    public function index(Request $request)
    {
        $user = old();

        $title = "Login to Mosaic";
        return view("login", [
            "title" => $title,
            "user" => $user
        ]);
    }

    //validate user login request, check for authorisation, route as required
    public function authorise(Request $request)
    {
        //from the global request function pull only the specified named inputs
        $loginDetails = $request->only("email", "password");

        //ensure email and password have been entered by user
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);

        //hash generator while users cannot be created outside of db
        //dd(Hash::make($request->password));

        if (Auth::attempt($loginDetails)) {
            //therefore login success
            $user = User::find(auth()->user()->id);

            if ($user->isAdmin()) {
                return redirect()->route('product.home');
            } else {
                return redirect()->route('dashboard.index');
            }
        } else {
            //login failed
            return back()->with('loginError', "Invalid login details");
        }
        //dd(Hash::make($request->user['password']));
        //auth check returns if a user is currently logged in or not
        //Auth::check();


    }
}
