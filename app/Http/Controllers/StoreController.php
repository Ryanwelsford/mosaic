<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Helpers\ModelValidator;
use App\Http\Helpers\ModelSearch\ModelSearchv4;
use App\Http\Controllers\Types\AdminAccessController;

/********************************************************
 *Stores handle the day to day operation of client requried tasks
 *This controller allows for the CRUD operations of the store class
 ********************************************************/

class StoreController extends AdminAccessController
{

    //produce menu options for each allowed class function
    public function home()
    {

        $title = "Stores Home";

        $newRoute = route("store.new");
        $viewRoute = route("store.view");

        $menuitems = [
            ["title" => "New Store", "anchor" => $newRoute, "img" => "/images/icons/new-256.png", "action" => "Create"],
            ["title" => "Edit Store", "anchor" => $viewRoute, "img" => "/images/icons/edit-256.png", "action" => "Edit"],
            ["title" => "Search Stores", "anchor" => $viewRoute, "img" => "/images/icons/search-256.png", "action" => "Search"]
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
            'store_name' => ['required'],
            'number' => ['required',  \Illuminate\Validation\Rule::unique('stores')->ignore($id)],
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
        $store->fillItem($id, $request->store_name, $request->number, $request->address1, $request->address2, $request->address3, $request->postcode, $user->id);

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
    //model search v2 is a tad broken
    public function view(Request $request, $response = '')
    {
        //order by number from high to low, query pulls user information aswell
        //$stores = Store::orderBy('number', 'desc')->with("users")->get();
        //stores user values can be access through store->users->email etc
        $store = new Store;
        $searchFields = $store->getSearchable();
        $search = $request->search;
        $sort = $request->sort;

        $searchArray = [
            "stores" => $searchFields,
            "users" => ["email"]
        ];

        //join users to store data
        $title = "Display Stores";
        $join = [
            "users" => ["users.id", "stores.user_id"]
        ];

        //so this version will allow for searching of user and store classes
        $modelSearchv4 = new ModelSearchv4(Store::class, $searchArray, $searchArray, [], $join);
        $stores = $modelSearchv4->search($search, $sort);


        //$searchFields[] = "email";

        return view("store.view", ["title" => $title, "stores" => $stores, "search" => $search, "sort" => $sort, "searchFields" => $searchFields, "response" => $response]);
    }

    //delete store as required
    //TODO add softdeletes for store
    //TODO add confirmation message to store deletion as per receipt del

    //now passes an id not the object due to search changes
    public function destroy($id, Request $request)
    {
        $store = Store::find($id);
        $user = User::find($store->user_id);
        $response = "Store number ". $store->number." deleted successfully";

        //delete store rather than user to ensure user details are fully removed as well, this cascades through and deletes store details
        $user->delete();

        return $this->view($request, $response);
    }


    //TODO add settings panel to change things like menu and delivery days?
    public function settings()
    {

        $title = "Store Settings";
    }
}
