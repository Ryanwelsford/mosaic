<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Helpers\ModelSearchv3;
use App\Http\Helpers\ModelValidator;
use App\Http\Helpers\ModelSearch\ModelSearchv4;
use App\Http\Controllers\Types\AdminAccessController;

//CRUD of admin/core user class
class AdminController extends AdminAccessController
{
    public function home()
    {
        $title = "Admin Home";


        $menuitems = [
            ["title" => "Create Admin", "anchor" => route("admin.new"), "img" => "/images/icons/new-256.png"],
            ["title" => "Edit Admin", "anchor" => route("admin.view"), "img" => "/images/icons/edit-256.png"],
            ["title" => "Search Admins", "anchor" => route("admin.view"), "img" => "/images/icons/search-256.png"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
        ]);
    }

    public function store(Request $request)
    {

        $title = "New Admin";

        $modelValidator = new ModelValidator(User::class, $request->id, old());
        $admin = $modelValidator->validate();

        return view('admin.new', ["title" => $title, "admin" => $admin]);
    }

    public function save(Request $request)
    {

        $this->validate($request, [
            'name' => ['required'],
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
            'email' => 'required'
        ]);

        $user = new User;

        if (isset($request->id)) {
            $user = User::find($request->id);
        }

        $user->fillItem($request->id, $request->name, $request->email, $request->password, "admin");
        $user->save();

        return $this->confirm($user);
    }

    private function confirm(User $user)
    {
        $title = "Confirmation";
        $heading = "Admin Created";
        $text = "Admin " . $user->name . " has been created successfully.";
        $anchor = route("admin.new");
        $anchorText = " to create another admin.";
        return view("general.confirmation-custom", [
            "title" => $title,
            "heading" => $heading,
            "text" => $text,
            "anchor" => $anchor,
            "anchorText" => $anchorText
        ]);
    }

    public function destroy(User $admin, Request $request)
    {
        $response = "Sucessfully deleted admin #" . $admin->id . " " . $admin->name;
        $admin->delete();
        return $this->view($request, $response);
    }

    public function view(Request $request, $response = "")
    {
        $title = "View Orders";
        $admins = new User;

        $fields = $admins->getSearchable();
        //remember to pass the search fields as a mapping of table to fields
        $searchFields["users"] = $fields;
        $search = $request->search;
        $sort = $request->sort;
        if ($sort == null) {
            $sort = "name";
        }

        //so v3 does work with a passed restriction
        $modelSearch = new ModelSearchv4(User::class, $searchFields, $searchFields, ["table" => "users", "field" => "privelleges", "value" => "admin"]);
        $admins = $modelSearch->search($search, $sort, "desc");

        return view("admin.view", [
            "title" => $title,
            "admins" => $admins,
            "searchFields" => $fields,
            "search" => $search,
            "sort" => $sort,
            "response" => $response
        ]);
    }
}
