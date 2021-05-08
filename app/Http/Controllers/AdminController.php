<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Helpers\ModelSearchv3;
use App\Http\Helpers\ModelValidator;
use App\Http\Helpers\ModelSearch\ModelSearchv4;
use App\Http\Controllers\Types\AdminAccessController;

//CRUD of admin/core user class
//admin serves as the base user class of mosaic
class AdminController extends AdminAccessController
{
    #display all home page menu items for the admin class
    public function home()
    {
        $title = "Admin Home";


        //menu items array
        $menuitems = [
            ["title" => "Create Admin", "anchor" => route("admin.new"), "img" => "/images/icons/new-256.png", "action" => "Create"],
            ["title" => "Edit Admin", "anchor" => route("admin.view"), "img" => "/images/icons/edit-256.png", "action" => "Edit"],
            ["title" => "Search Admins", "anchor" => route("admin.view"), "img" => "/images/icons/search-256.png", "action" => "Search"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
        ]);
    }

    //display base admin create form with the ability to edit through the same concept
    public function store(Request $request)
    {

        $title = "New Admin";

        //use model validator to produc eeither failed form submission, new empty admin form, or editable pre created admin
        $modelValidator = new ModelValidator(User::class, $request->id, old());
        $admin = $modelValidator->validate();

        return view('admin.new', ["title" => $title, "admin" => $admin]);
    }

    //save post request of updated admin/new admin
    public function save(Request $request)
    {
        //TODO add unique error messages for users, maybe add unique rule here as well for username or at least for email
        $this->validate($request, [
            'name' => ['required'],
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
            'email' => 'required'
        ]);

        $user = new User;

        //when admin is edited pull model from db
        if (isset($request->id)) {
            $user = User::find($request->id);
        }

        //fill and save
        $user->fillItem($request->id, $request->name, $request->email, $request->password, "admin");
        $user->save();

        //return confirmation page
        return $this->confirm($user);
    }

    //display admin success page when admin is created
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

    //delete passed admin and return to search page with admin deletion message
    public function destroy(User $admin, Request $request)
    {
        $response = "Sucessfully deleted admin #" . $admin->id . " " . $admin->name;
        $admin->delete();
        return $this->view($request, $response);
    }

    //produce searable page for admins
    public function view(Request $request, $response = "")
    {
        $title = "View Orders";
        $admins = new User;
        //pull and map search fields of the admin class
        $fields = $admins->getSearchable();
        //remember to pass the search fields as a mapping of table to fields
        $searchFields["users"] = $fields;

        //setup default parameters for searches
        $search = $request->search;
        $sort = $request->sort;
        if ($sort == null) {
            $sort = "name";
        }

        //use model search v3 allow users to search all admins only, dont allow store level access here as that can be done within the store controller
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
