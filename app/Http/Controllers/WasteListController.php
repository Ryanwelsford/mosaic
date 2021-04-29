<?php

namespace App\Http\Controllers;

use App\Models\Wastelist;
use Illuminate\Http\Request;
use App\Http\Helpers\ModelValidator;
use App\Http\Helpers\ModelSearch\ModelSearchv4;
use App\Http\Controllers\Types\AdminAccessController;

/********************************************************
 *Controller handles all waste list requirements
 *Waste lists are descriptive groupings for stores to input invalide products due to overproduction or etc
 ********************************************************/

class WasteListController extends AdminAccessController
{

    //produce a menu for all actions within teh controller
    public function home()
    {

        $title = "Waste List Home";

        //modify this to prevent having to redo any anchors when needed
        $newRoute = Route("wastelist.new");
        $viewRoute = Route("wastelist.view");

        //setup menu options with routes and icons
        $menuitems = [
            ["title" => "New Waste List", "anchor" => $newRoute, "img" => "/images/icons/new-256.png", "action" => "Create"],
            ["title" => "Edit Waste List", "anchor" => $viewRoute, "img" => "/images/icons/edit-256.png", "action" => "Edit"],
            ["title" => "View Waste Lists", "anchor" => $viewRoute, "img" => "/images/icons/view-256.png", "action" => "View"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
        ]);
    }

    //diplay waste list creation/edit form
    public function store(Request $request)
    {
        $title = "New Waste List";
        $statuses = ["Active", "Inactive"];
        //validate request ensure either an empty wastelist, an editable instance of a wastelist or a failed post form refilled with input details is returned
        $ModelValidator = new ModelValidator(Wastelist::class, $request->id, old());
        $wastelist = $ModelValidator->validate();

        return view("wastelist.new", [
            "title" => $title,
            "wastelist" => $wastelist,
            "statuses" => $statuses
        ]);
    }

    //validate store request, flash into previous page or save wastelist
    public function save(Request $request)
    {
        $id = $request->id;

        //ensure name and description are entered, name must be unique
        //ignore prevents issues when updating an existing wastelist
        $this->validate($request, [
            'name' => ['required', \Illuminate\Validation\Rule::unique('wastelists')->ignore($id)],
            'description' => 'required'
        ]);

        $wastelist = new Wastelist;

        //if an update is required
        if (isset($id)) {
            $wastelist = Wastelist::find($id);
        }

        //there is actually a fill function which can be used to fill all fillable waste list items in an associative array
        // "id" => $id etc
        $wastelist->fillItem($id, $request->name, $request->description, $request->status);

        //save and return confirmation
        $wastelist->save();

        return $this->confirm($wastelist);
    }

    //confirmation screen after successful update/creation
    public function confirm(?Wastelist $wastelist)
    {
        //?Wastelist means a null wastlist can be sent to the method
        //this doesnt actually matter, the confirm message does not have its own route (and doesnt need to), this prevents a random confirmation page being accessible at any point
        if ($wastelist == null) {
        }

        $title = "Waste List Confirmation";
        $heading = "Waste List Successfully Created";
        $text = "Waste List has been created successfully";
        $anchor = route('wastelist.new');
        return view("general.confirmation", ["title" => $title, "heading" => $heading, "text" => $text, "anchor" => $anchor]);
    }

    //view all wastelists with options to edit and delete, need to add search and validation features in the future
    public function view(Request $request, $response = '')
    {
        $wastelist = new Wastelist;

        $fields = $wastelist->getSearchable();
        //remember to pass the search fields as a mapping of table to fields
        $searchFields["wastelists"] = $fields;
        $search = $request->search;
        $sort = $request->sort;
        if ($sort == null) {
            $sort = "name";
        }

        //so v3 does work with a passed restriction
        $modelSearch = new ModelSearchv4(Wastelist::class, $searchFields, $searchFields);
        $wastelists = $modelSearch->search($search, $sort, "desc");

        $title = "Display Waste Lists";
        return view("wastelist.view", ["title" => $title, "wastelists" => $wastelists, "response" => $response, "search" => $search, "sort" => $sort, "searchFields" => $fields]);
    }

    //delete method for wastelists passed, should probably have archive here or softdeletes instead
    public function destroy(Wastelist $wastelist, Request $request)
    {
        $wastelist->delete();
        $message = "Wastelist " . ucfirst($wastelist->name) . " has been successfully deleted";
        //return to view screen
        return $this->view($request, $message);
    }
}
