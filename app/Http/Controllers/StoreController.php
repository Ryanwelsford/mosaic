<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Helpers\MyClassess;
use App\Http\Helpers\ModelValidator;

class StoreController extends Controller
{
    public function home()
    {

        $title = "Stores Home";

        $newRoute = route("store.new");

        $menuitems = [
            ["title" => "New Store", "anchor" => $newRoute, "img" => "/images/icons/new-256.png"],
            ["title" => "Edit Store", "anchor" => "/test", "img" => "/images/icons/edit-256.png"],
            ["title" => "Search Stores", "anchor" => "/test", "img" => "/images/icons/search-256.png"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
        ]);
    }

    public function store(Request $request)
    {
        $title = "Create A Store";

        $modelValidator = new ModelValidator(Store::class, $request->id, old());
        $store = $modelValidator->validate();

        return View("store.new", [
            "title" => $title,
            "store" => $store
        ]);
    }

    public function save(Request $request)
    {
        $id = $request->id;

        $this->validate($request, [
            'name' => ['required'],
            'number' => 'required',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
            'address1' => 'required',
            'postcode' => 'required'
        ]);

        $store = new Store;
        $user = new User;

        $email = "hut" . $request->number . "@phr.co.uk";
        $username = "Hut " . $request->number;

        if (isset($id)) {
            $store = Store::find($id);
            $user = $store->users()->get()->first();
        }

        if (isset($user->id)) {
            $userId = $user->id;
        } else {
            $userId = null;
        }

        $user->fillItem($userId, $username, $email, $request->password, "store");

        $user->save();

        $store->fillItem($id, $request->name, $request->number, $request->address1, $request->address2, $request->address3, $request->postcode, $user->id);

        $store->save();

        return $this->confirm($store);
        //need to build store name, email
    }

    public function confirm(Store $store)
    {
        $title = "Store Confirmation";
        $heading = "Store Successfully Created";
        $text = "Store has been created successfully";
        $anchor = route('store.new');
        return view("general.confirmation", ["title" => $title, "heading" => $heading, "text" => $text, "anchor" => $anchor]);
    }

    public function view()
    {
        $stores = Store::orderBy('number', 'desc')->with("users")->get();
        //stores user values can be access through store->users->email etc
        $title = "Display Stores";


        return view("store.view", ["title" => $title, "stores" => $stores]);
    }

    public function destroy(Store $store)
    {
        $store->delete();

        return back();
    }
}
