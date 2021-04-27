<?php

namespace App\Http\Controllers;

use App\Models\Waste;
use App\Models\Product;
use App\Models\Wastelist;
use Illuminate\Http\Request;
use App\Http\Helpers\ModelSearchv3;
use App\Http\Helpers\ModelValidator;
use App\Http\Helpers\ModelSearch\ModelSearchv4;
use App\Http\Controllers\Types\UserAccessController;

class WasteController extends UserAccessController
{
    public function home()
    {

        $title = "Waste Home";

        $menuitems = [
            ["title" => "New Waste", "anchor" => route('waste.new'), "img" => "/images/icons/new-256.png"],
            ["title" => "Edit Waste", "anchor" => route('waste.view'), "img" => "/images/icons/edit-256.png"],
            ["title" => "View Waste", "anchor" => route('waste.view'), "img" => "/images/icons/view-256.png"],
            ["title" => "Waste Reports", "anchor" => "/test", "img" => "/images/icons/report-256.png"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
        ]);
    }
    //pull information for ajax query.
    public function categoryReturn(Request $request)
    {
        $products = Product::where('category', $request->category)->orderby('name', 'desc')->with("units")->get();

        $products = $products->toJson();

        return response()->json($products);
    }
    //TODO edit does not work yet!
    public function store(Request $request)
    {
        $title = "New Waste Entry";
        //change this to pull only active wastelists or something
        $wasteLists = Wastelist::all();

        $productController = new ProductController($request);
        $categories = $productController->buildCategories();

        $modelValidator = new ModelValidator(Waste::class, $request->id, old());
        $wastes = $modelValidator->validate();
        $results = $resultsMap = false;
        //form validation has failed user input
        if (!empty(old())) {
            $products = Product::query();

            $old = old();
            $resultsMap = $old['product'];
            //setup where clauses for each previously entered product
            foreach ($resultsMap as $pid => $quantity) {
                $products = $products->orWhere("id", "=", $pid);
            }
            $results = $products->orderby('name', 'desc')->with("units")->get();
        }
        //i.e. edit request
        else if (isset($request->id) && $wastes) {
            $results = $wastes->products()->orderby('name', 'desc')->with("units")->get();
        }


        return view('waste.new', [
            "title" => $title,
            "wastes" => $wastes,
            "categories" => $categories,
            "wastelists" => $wasteLists,
            "results" => $results,
            "resultsMap" => $resultsMap
        ]);
    }

    public function save(Request $request)
    {
        $this->validate(
            $request,
            [
                'reference' => ['required'],
                'product' => ['required']
            ],
            [
                "reference.required" => "A reference must be entered",
                "product.required" => "At least one product must be entered"
            ]
        );

        $waste = new Waste;

        if (isset($request->id)) {
            $waste = Waste::find($request->id);
        }

        $waste->fillItem($request->id, $request->reference, $request->wastelist_id, $this->store->id);
        $waste->save();

        $products = $request->product;
        $organisedMappings = [];
        foreach ($products as $product_id => $quantity) {

            $required = true;
            if (is_null($quantity) || $quantity == 0) {
                $required = false;
            }
            if ($required) {
                $organisedMappings[$product_id] = ["quantity" => $quantity];
            }
        }

        $waste->products()->sync($organisedMappings);

        return $this->confirm($waste);
    }

    public function confirm(Waste $waste)
    {

        $products = $waste->products()->with("units")->get();
        [$sum, $quantity] = $this->calc($products);

        $title = "Waste Confirmation";
        $heading = "Waste Successfully Booked";
        $text = "Waste has been created successfully for a total value of £" . number_format($sum, 2) . " and " . $quantity . " cases in total";
        $anchor = route('waste.new');
        return view("general.confirmation-print", ["title" => $title, "heading" => $heading, "text" => $text, "anchor" => $anchor]);
    }

    public function calc($values)
    {
        $quantity = 0;
        $sum = 0;

        foreach ($values as $each) {
            $quantity += $each->pivot->quantity;
            $sum += $each->pivot->quantity * $each->units->price;
        }

        return [$sum, $quantity];
    }



    public function view(Request $request, $response = '')
    {
        $title = "Search Wastes";
        $waste = new Waste;
        $wastelist = new Wastelist;
        $searchFields = [
            "wastes" => $waste->getSearchable(),
            "wastelists" => $wastelist->getSearchable()
        ];
        $join = ["wastelists" => ["wastelists.id", "wastes.wastelist_id"]];
        $search = $request->search;
        $sort = $request->sort;
        $sortDirection = "desc";

        if ($sort == null) {
            $sort = "id";
        }
        $modelSearchv4 = new ModelSearchv4(Waste::class, $searchFields, $searchFields, ["table" => "wastes", "field" => "store_id", "value" => $this->store->id], $join);
        $wastes = $modelSearchv4->search($search, $sort, $sortDirection);

        $searchFields = array_merge($waste->getSearchable(), $wastelist->getSearchable());
        return view("waste.view", ["title" => $title, "wastes" => $wastes, "search" => $search, "sort" => $sort, "searchFields" => $searchFields, "response" => $response]);
    }

    public function summary(Request $request)
    {
        $title = "Waste Summary";
        [$waste, $products, $sum, $quantity] = $this->wasteDetails($request->id);
        return view("waste.summary", ["title" => $title, "store" => $this->store, "waste" => $waste, "listing" => $products, "sum" => $sum, "quantity" => $quantity]);
    }
    public function wasteDetails($id)
    {

        $waste = Waste::find($id);
        $products = $waste->products()->with("units")->get();
        [$sum, $quantity] = $this->calc($products);

        return [$waste, $products, $sum, $quantity];
    }
    public function print(Request $request)
    {
        $title = "Waste Summary";
        [$waste, $products, $sum, $quantity] = $this->wasteDetails($request->id);

        return view("waste.print", ["title" => $title, "store" => $this->store, "waste" => $waste, "listing" => $products, "sum" => $sum, "quantity" => $quantity]);
    }

    public function destroy($id, Request $request)
    {
        $waste = Waste::find($id);
        $response = "Successfully deleted waste reference \"" . $waste->reference . "\"";
        $waste->delete();
        return $this->view($request, $response);
    }
}
