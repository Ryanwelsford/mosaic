<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Waste;
use App\Models\Product;
use App\Models\Wastelist;
use Illuminate\Http\Request;
use App\Http\Helpers\ModelSearchv3;
use App\Http\Helpers\ModelValidator;
use App\Http\Helpers\ModelSearch\ModelSearchv4;
use App\Http\Controllers\Types\UserAccessController;

//waste controller allows for the entry of products that have been discarded for any number of reasons
//also provides reports on those entries
class WasteController extends UserAccessController
{
    //display waste home page with links
    public function home()
    {

        $title = "Waste Home";

        $menuitems = [
            ["title" => "New Waste", "anchor" => route('waste.new'), "img" => "/images/icons/new-256.png", "action" => "Create"],
            ["title" => "Edit Waste", "anchor" => route('waste.view'), "img" => "/images/icons/edit-256.png", "action" => "Edit"],
            ["title" => "Waste Summaries", "anchor" => route('waste.view'), "img" => "/images/icons/view-256.png", "action" => "View"],
            ["title" => "Weekly Waste Report", "anchor" => route('waste.date'), "img" => "/images/icons/report-256.png"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
        ]);
    }

    //pull information for ajax query.
    //called by javascript returns products in given category and their relative units
    public function categoryReturn(Request $request)
    {
        $products = Product::where('category', $request->category)->orderby('name', 'asc')->with("units")->get();

        //has to be returned in this format
        $products = $products->toJson();

        return response()->json($products);
    }

    //allow for the creation/edit of class
    public function store(Request $request)
    {
        $title = "New Waste Entry";
        //change this to pull only active wastelists or something
        $wasteLists = Wastelist::where('status', 'Active')->get();

        //get categories
        $productController = new ProductController();
        $categories = $productController->buildCategories();

        //get class instance
        $modelValidator = new ModelValidator(Waste::class, $request->id, old());
        $wastes = $modelValidator->validate();

        $results = $resultsMap = false;
        //form validation has failed user input
        $old = old();
        //fixes for form failures
        if (!empty(old()) && isset($old['product'])) {
            $products = Product::query();

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

    //save waste entry
    public function save(Request $request)
    {
        //ensure at least 1 entry is submitted and that the references are filled
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

        //if edit
        if (isset($request->id)) {
            $waste = Waste::find($request->id);
        }

        //save waste
        $waste->fillItem($request->id, $request->reference, $request->wastelist_id, $this->store->id);
        $waste->save();

        //remap product quantites into syncable format
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

        //save waste product mappings
        $waste->products()->sync($organisedMappings);

        return $this->confirm($waste);
    }

    //display success messages
    public function confirm(Waste $waste)
    {

        $products = $waste->products()->with("units")->get();
        [$sum, $quantity] = $this->calc($products);

        $title = "Waste Confirmation";
        $heading = "Waste Successfully Booked";
        $text = "Waste has been created successfully for a total value of £" . number_format($sum, 2) . " and " . $quantity . " cases in total";
        $anchor = route('waste.print', [$waste->id]);
        return view("general.confirmation-print", ["title" => $title, "heading" => $heading, "text" => $text, "anchor" => $anchor]);
    }

    //calc waste values
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


    //allow for searching of waste class
    public function view(Request $request, $response = '')
    {
        $title = "Search Wastes";
        $waste = new Waste;
        $wastelist = new Wastelist;
        //waste list and waste are required
        $searchFields = [
            "wastes" => $waste->getSearchable(),
            "wastelists" => $wastelist->getSearchable()
        ];
        //setup join
        $join = ["wastelists" => ["wastelists.id", "wastes.wastelist_id"]];
        //get search vars
        $search = $request->search;
        $sort = $request->sort;
        $sortDirection = "desc";

        if ($sort == null) {
            $sort = "id";
        }

        //restrict to only display logged stores data with join
        $modelSearchv4 = new ModelSearchv4(Waste::class, $searchFields, $searchFields, ["table" => "wastes", "field" => "store_id", "value" => $this->store->id], $join);
        $wastes = $modelSearchv4->search($search, $sort, $sortDirection);

        //produce single array for page display
        $searchFields = array_merge($waste->getSearchable(), $wastelist->getSearchable());

        return view("waste.view", ["title" => $title, "wastes" => $wastes, "search" => $search, "sort" => $sort, "searchFields" => $searchFields, "response" => $response]);
    }

    //display a single summary for waste details
    public function summary(Waste $waste)
    {
        if (!isset($waste->id) || is_null($waste)) {
            return redirect()->route("waste.view");
        }

        $title = "Waste Summary";
        [$waste, $products, $sum, $quantity] = $this->wasteDetails($waste->id);
        return view("waste.summary", ["title" => $title, "store" => $this->store, "waste" => $waste, "listing" => $products, "sum" => $sum, "quantity" => $quantity]);
    }

    //gatjer waste information for the passed in waste entry
    public function wasteDetails($id)
    {

        $waste = Waste::find($id);
        $products = $waste->products()->with("units")->get();
        [$sum, $quantity] = $this->calc($products);

        return [$waste, $products, $sum, $quantity];
    }
    //display summary in printable format
    public function print(Waste $waste)
    {
        if (!isset($waste->id) || is_null($waste)) {
            return redirect()->route("waste.view");
        }


        $title = "Waste Summary";
        [$waste, $products, $sum, $quantity] = $this->wasteDetails($waste->id);

        return view("waste.print", ["title" => $title, "store" => $this->store, "waste" => $waste, "listing" => $products, "sum" => $sum, "quantity" => $quantity]);
    }

    //delete waste entry
    public function destroy($id, Request $request)
    {
        $waste = Waste::find($id);
        $response = "Successfully deleted waste reference \"" . $waste->reference . "\"";
        $waste->delete();
        return $this->view($request, $response);
    }

    public function dateSelect()
    {
        $title = "Select Date";
        $heading = "Select Week";
        $label = "Pick which week to view waste data";
        $route = route('waste.weekly');
        return view('general.date-select', ["title" => $title, "heading" => $heading, "route" => $route, "label" => $label]);
    }

    public function weekly(Request $request)
    {
        //date check function within forecasting controller used as a guard clause
        $fc = new ForecastingController();

        //guard for malformed dates/ unset dates
        if (!isset($request->date) || !$fc->checkIsAValidDate($request->date)) {
            return redirect()->route("waste.home");
        }
        //build start and end times
        $startDate = Carbon::parse($request->date)->startOfWeek();
        $endDate = Carbon::parse($request->date)->endOfWeek();

        //chain off a query
        //you cant call the products() function as its a collection of Waste items not just a singular waste item
        //therefore you have to loop through the waste entries and then call the products function
        //im sure theres a better way to batch load them or something;
        $wastes = Waste::where("store_id", $this->store->id)
            ->whereBetween("created_at", [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderby("created_at", 'asc')
            ->get();

        //get waste data and chart
        [$totalValue, $wasteDateMap, $catMap] = $this->wasteCalc($wastes);

        $chartData1 = $this->chartData1($wasteDateMap);
        $chartData2 = $this->chartData2($catMap);

        $title = "Weekly Summary";
        return view('waste.weekly-summary', [
            "title" => $title,
            "startDate" => $startDate,
            "endDate" => $endDate,
            "totalValue" => $totalValue,
            "wastes" => $wastes,
            "chartData1" => $chartData1,
            "chartData2" => $chartData2
        ]);
    }

    //get total ifnromation and category map for waste input
    public function wasteCalc($wastes)
    {
        $totalValue = 0;
        $wasteDateMap = [];
        $catMap = [];
        //step through waste
        foreach ($wastes as $waste) {
            $products = $waste->products()->with("units")->get();
            $wasteTotal = 0;
            $wasteCases = 0;
            //step through products in waste
            foreach ($products as $product) {
                $wasteTotal += $product->pivot->quantity * $product->units->price;
                $wasteCases += $product->pivot->quantity;

                //remap values based on category
                if (!isset($catMap[$product->category])) {
                    $catMap[$product->category] = 0;
                }

                $catMap[$product->category] += $product->pivot->quantity * $product->units->price;
                //var_dump($catMap);
            }
            //map waste date to value
            $wasteDateMap[$waste->created_at->format('D')][] = $wasteTotal;
            $waste->total  = $wasteTotal;
            $waste->quantity = $wasteCases;
            $totalValue += $wasteTotal;
        }

        return [$totalValue, $wasteDateMap, $catMap];
    }

    //map data from days into correct format for google charts
    public function chartData1($wasteDateMap)
    {
        $days = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];

        $chartData1 = [];
        $chartData1[] = ["Day", "Pounds Wasted"];

        //loop through days and check for instance in waste map, total up values for all waste entries
        foreach ($days as $day) {
            $value = 0;

            if (isset($wasteDateMap[$day])) {
                //allow for summing up of multiple days
                foreach ($wasteDateMap[$day] as $each) {
                    $value += $each;
                }
            }
            //string list day to value of wastage
            $chartData1[] = [$day, round($value)];
        }

        return json_encode($chartData1);
    }

    //map category to value within string list
    public function chartData2($catMap)
    {
        $chartData2 = [];
        $chartData2[] = ["Category", "Pounds Wasted"];

        //map as category to valye of category input
        foreach ($catMap as $category => $value) {
            $chartData2[] = [$category, $value];
        }

        return json_encode($chartData2);
    }
}
