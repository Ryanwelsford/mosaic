<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Helpers\ModelValidator;

/********************************************************
 *Stores handle the day to day operation of client requried tasks
 *This controller allows for the CRUD operations of the store class
 ********************************************************/

class StoreController extends Controller
{

    //produce menu options for each allowed class function
    public function home()
    {

        $title = "Stores Home";

        $newRoute = route("store.new");
        $viewRoute = route("store.view");

        $menuitems = [
            ["title" => "New Store", "anchor" => $newRoute, "img" => "/images/icons/new-256.png"],
            ["title" => "Edit Store", "anchor" => $viewRoute, "img" => "/images/icons/edit-256.png"],
            ["title" => "Search Stores", "anchor" => $viewRoute, "img" => "/images/icons/search-256.png"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
        ]);
    }

    //display new store page requires store details entry as well as details for the required system user.
    public function store(Request $request)
    {
        $title = "Create A Store";


        //validate the store class, return either the required editable class instance, a failed post form refilled or a new instance to be created
        $modelValidator = new ModelValidator(Store::class, $request->id, old());
        $store = $modelValidator->validate();

        return View("store.new", [
            "title" => $title,
            "store" => $store
        ]);
    }

    //save the new store and user required
    public function save(Request $request)
    {
        $id = $request->id;

        //validate all the different fields in form
        //password confirmation ensures the password entered twice matches
        //TODO add unique rules to ensure store number is unique
        $this->validate($request, [
            'name' => ['required'],
            'number' => 'required',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
            'address1' => 'required',
            'postcode' => 'required'
        ]);

        //create instances of store and user
        $store = new Store;
        $user = new User;

        //build computed fields
        $email = "hut" . $request->number . "@phr.co.uk";
        $username = "Hut " . $request->number;

        //if id is set, therefore an edit is required
        if (isset($id)) {
            //pull store details and user details
            $store = Store::find($id);
            $user = $store->users()->get()->first();
        }

        //null check for user information
        if (isset($user->id)) {
            $userId = $user->id;
        } else {
            $userId = null;
        }

        //TODO reseach methods within laravel to create store and user in one method
        //set user model information
        $user->fillItem($userId, $username, $email, $request->password, "store");

        $user->save();

        //set and save store information
        $store->fillItem($id, $request->name, $request->number, $request->address1, $request->address2, $request->address3, $request->postcode, $user->id);

        $store->save();

        return $this->confirm($store);
    }

    //produce confirmation message on store creation/edit
    public function confirm(Store $store)
    {
        $title = "Store Confirmation";
        $heading = "Store Successfully Created";
        $text = "Store has been created successfully";
        $anchor = route('store.new');
        return view("general.confirmation", ["title" => $title, "heading" => $heading, "text" => $text, "anchor" => $anchor]);
    }

    //display stores in number desc order alongside the user information
    public function view()
    {
        //order by number from high to low, query pulls user information aswell
        $stores = Store::orderBy('number', 'desc')->with("users")->get();
        //stores user values can be access through store->users->email etc
        $title = "Display Stores";


        return view("store.view", ["title" => $title, "stores" => $stores]);
    }

    //delete store as required
    //TODO add softdeletes for store
    //TODO add confirmation message to store deletion as with menu/product deletion
    public function destroy(Store $store)
    {
        $store->delete();

        return back();
    }
}
