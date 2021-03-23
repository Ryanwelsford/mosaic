<?php

namespace App\Http\Controllers;

use App\Models\Wastelist;
use Illuminate\Http\Request;
use App\Http\Helpers\ModelValidator;

/********************************************************
 *Controller handles all waste list requirements
 *Waste lists are descriptive groupings for stores to input invalide products due to overproduction or etc
 ********************************************************/

class WasteListController extends Controller
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
            ["title" => "New Waste List", "anchor" => $newRoute, "img" => "/images/icons/new-256.png"],
            ["title" => "Edit Waste List", "anchor" => $viewRoute, "img" => "/images/icons/edit-256.png"],
            ["title" => "View Waste Lists", "anchor" => $viewRoute, "img" => "/images/icons/view-256.png"]
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

        //validate request ensure either an empty wastelist, an editable instance of a wastelist or a failed post form refilled with input details is returned
        $ModelValidator = new ModelValidator(Wastelist::class, $request->id, old());
        $wastelist = $ModelValidator->validate();

        return view("wastelist.new", [
            "title" => $title,
            "wastelist" => $wastelist
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
        $wastelist->fillItem($id, $request->name, $request->description);

        //save and return confirmation
        $wastelist->save();

        return $this->confirm($wastelist);
    }

    //confirmation screen after successful update/creation
    public function confirm(?Wastelist $wastelist)
    {
        if ($wastelist == null) {
        }

        $title = "Waste List Confirmation";
        $heading = "Waste List Successfully Created";
        $text = "Waste List has been created successfully";
        $anchor = route('wastelist.new');
        return view("general.confirmation", ["title" => $title, "heading" => $heading, "text" => $text, "anchor" => $anchor]);
    }

    //view all wastelists with options to edit and delete, need to add search and validation features in the future
    public function view()
    {
        $wastelists = Wastelist::orderBy('created_at', 'desc')->get();
        $title = "Display Waste Lists";
        return view("wastelist.view", ["title" => $title, "wastelists" => $wastelists]);
    }

    //delete method for wastelists passed
    public function destroy(Wastelist $wastelist)
    {
        $wastelist->delete();

        //return to view screen
        return back();
    }
}
