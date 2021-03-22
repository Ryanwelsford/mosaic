<?php

namespace App\Http\Controllers;

use App\Models\Wastelist;
use Illuminate\Http\Request;
use App\Http\Helpers\ModelValidator;

class WasteListController extends Controller
{
    public function home()
    {

        $title = "Waste List Home";

        //modify this to prevent having to redo any anchors when needed
        $newRoute = Route("wastelist.new");
        $viewRoute = Route("wastelist.view");


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

    public function store(Request $request)
    {
        $title = "New Waste List";

        $ModelValidator = new ModelValidator(Wastelist::class, $request->id, old());

        $wastelist = $ModelValidator->validate();

        return view("wastelist.new", [
            "title" => $title,
            "wastelist" => $wastelist
        ]);
    }

    public function save(Request $request)
    {
        $id = $request->id;

        $this->validate($request, [
            'name' => ['required', \Illuminate\Validation\Rule::unique('wastelists')->ignore($id)],
            'description' => 'required'
        ]);

        $wastelist = new Wastelist;

        if (isset($id)) {
            $wastelist = Wastelist::find($id);
        }

        $wastelist->fillItem($id, $request->name, $request->description);

        $wastelist->save();


        return $this->confirm($wastelist);
    }

    public function confirm(Wastelist $wastelist)
    {
        $title = "Waste List Confirmation";
        $heading = "Waste List Successfully Created";
        $text = "Waste List has been created successfully";
        $anchor = route('wastelist.new');
        return view("general.confirmation", ["title" => $title, "heading" => $heading, "text" => $text, "anchor" => $anchor]);
    }

    public function view()
    {
        $wastelists = Wastelist::orderBy('created_at', 'desc')->get();
        $title = "Display Waste Lists";


        return view("wastelist.view", ["title" => $title, "wastelists" => $wastelists]);
    }

    public function destroy(Wastelist $wastelist)
    {
        $wastelist->delete();

        return back();
    }
}
