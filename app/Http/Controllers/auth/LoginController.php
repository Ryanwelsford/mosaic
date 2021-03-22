<?php

namespace App\Http\Controllers\auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function index(Request $request)
    {
        $user = old();

        $title = "Login to Mosaic";
        return view("login", [
            "title" => $title,
            "user" => $user
        ]);
    }

    public function authorise(Request $request)
    {
        $loginDetails = $request->only("email", "password");

        $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);

        //hash generator while users cannot be created outside of db
        //dd(Hash::make($request->password));

        if (Auth::attempt($loginDetails)) {
            //therefore login success
            echo "Test";
            return redirect()->route('product.home');
        } else {
            //login failed

            return back()->with('loginError', "Invalid login details");
        }
        //dd(Hash::make($request->user['password']));
        //auth check returns if a user is currently logged in or not
        //Auth::check();


    }
}
