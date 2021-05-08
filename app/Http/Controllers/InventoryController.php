<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Store;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Http\Helpers\ModelValidator;
use App\Http\Helpers\ModelSearch\ModelSearchv4;
use App\Http\Controllers\Types\UserAccessController;

/**********************************************************
 *This controler serves as the hub for all core inventory counting and reporting for each store
 *This includes data charting
 **********************************************************/
class InventoryController extends UserAccessController
{
    //pull inventory home page
    public function home()
    {

        $title = "Inventory Home";
        //pull latest count for this store
        $inventory = Inventory::orderby('created_at', 'desc')->where('store_id', $this->store->id)->where('status', 'Booked')->get()->first();

        //guard to prevent report with current count appearing in menu list
        if (is_null($inventory)) {
            $menuitems = [
                ["title" => "New Count", "anchor" => route("inventory.new"), "img" => "/images/icons/new-256.png", "action" => "Create"],
                ["title" => "Edit Saved Count", "anchor" => route("inventory.view", ["search" => "saved"]), "img" => "/images/icons/edit-256.png", "action" => "Edit"],
                ["title" => "Previous Count Summaries", "anchor" => route("inventory.view"), "img" => "/images/icons/view-256.png"],

            ];
        } else {
            //otehrwise display all menu items
            $menuitems = [
                ["title" => "Current Count", "anchor" => route("inventory.summary", [$inventory->id]), "img" => "/images/icons/summary-256.png", "action" => "View"],
                ["title" => "New Count", "anchor" => route("inventory.new"), "img" => "/images/icons/new-256.png", "action" => "Create"],
                ["title" => "Edit Saved Count", "anchor" => route("inventory.view", ["search" => "saved"]), "img" => "/images/icons/edit-256.png", "action" => "Edit"],
                ["title" => "Previous Count Summaries", "anchor" => route("inventory.view"), "img" => "/images/icons/view-256.png"],

            ];
        }

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
        ]);
    }

    //Pull base count information and/or new count
    public function store(Request $request)
    {
        $title = "Inventory Count";
        $today = new Carbon();
        //pull all possible counts
        $products = Product::orderby('category')->orderby('subcategory')->orderby('name')->with("units")->get();
        $firstCategory = "Chilled";

        $mappedProducts = [];

        //pull inventory info for failed forms, new inv and edit inv
        $modelValidator = new ModelValidator(Inventory::class, $request->id, old());
        $inventory = $modelValidator->validate();

        //i.e. edit requested
        if (isset($request->id) && $inventory != false) {
            $inventory = Inventory::find($request->id);
            $enteredProducts = $inventory->products()->get();

            //if edit therefore there are counted products remap prod->id to quantity for placement in count form
            foreach ($enteredProducts as $prod) {
                $mappedProducts[$prod->id] = $prod->pivot->quantity;
            }
        }

        //old is not required as no validation takes place

        return view("inventory.new", [
            "title" => $title,
            "products" => $products,
            "today" => $today,
            "store" => $this->store,
            "firstCategory" => $firstCategory,
            "mappedProducts" => $mappedProducts,
            "inventory" => $inventory
        ]);
    }

    //save inventory count information
    public function save(Request $request)
    {

        //pull listing information from form
        $productsToQuantity = $request->product;
        $status = $request->status;
        $inventory = new Inventory;

        //pull relevent inventory is edit
        if (isset($request->id)) {
            $inventory = Inventory::find($request->id);
        }

        //setup and save updated inventory details
        $inventory->fillitem($request->id, $this->store->id, $status);

        $inventory->save();

        //remap product ids to quanitity counted in form
        $organisedMappings = [];
        foreach ($productsToQuantity as $product_id => $quantity) {
            //fix for empty input fields, solves issue of form not filling with 0s properly
            //form issues fixed kept as guard
            if (is_null($quantity)) {
                $quantity = 0;
            }
            $organisedMappings[$product_id] = ["quantity" => $quantity];
        }

        //save product to inventory count mappings
        $inventory->products()->sync($organisedMappings);

        return $this->confirm($inventory);
    }

    //return display with inventory informaton including route to new count summary
    public function confirm(Inventory $inventory)
    {
        //dd($inventory);
        $title = "Count Confirmation";
        //build inv information
        [$catSummary, $quantity, $sum] = $this->fullCalc($inventory->products()->orderby('category', 'asc')->with("units")->get());

        $heading = "Count Successfully " . $inventory->status;
        $text = "Count has been created successfully for a total value of £" . number_format($sum, 2) . " and " . number_format($quantity, 2) . " cases in total";
        $anchor = route('inventory.summary', [$inventory->id]);
        $anchorText = " to view the count summary";

        return view("general.confirmation-custom", ["title" => $title, "heading" => $heading, "text" => $text, "anchor" => $anchor, "anchorText" => $anchorText]);
    }

    //produce summary report for a single inventory
    public function countSummary(Inventory $inventory)
    {
        //for the given inventory pull the related product with counts and unit information
        $productMappings = $inventory->products()->orderby('category', 'asc')->with("units")->get();

        //pull chart data and inventory calc information
        [$catSummary, $totalQuantity, $totalValue] = $this->fullCalc($productMappings);
        [$chartData1, $chartData2] = $this->gatherChartData($catSummary);
        $title = "Count Summary";

        return view("inventory.summary", [
            "title" => $title,
            "catSummary" => $catSummary,
            "sum" => $totalValue,
            "quantity" => $totalQuantity,
            "store" => $this->store,
            "inventory" => $inventory,
            "chartData1" => $chartData1,
            "chartData2" => $chartData2
        ]);
    }

    //setup data as per google chart requiremnets
    public function gatherChartData($categorySummaries)
    {

        //initialise core chart vars
        $chartData1 = [];
        $chartData1[] = ['Category', 'Cases'];

        $chartData2 = [];
        $chartData2[] = ['Category', 'Sum'];

        //loop through mapping summed category information to the total value/cases
        foreach ($categorySummaries as $category => $each) {
            //round to prevent crazy decimals appearing in charts
            $chartData1[] = [$category, round($each['quantity'], 2)];
            $chartData2[] = [$category, round($each['sum'], 2)];
        }

        //encode into jsons and return.
        $jsonTable1 = json_encode($chartData1);
        $jsonTable2 = json_encode($chartData2);

        return [$jsonTable1, $jsonTable2];
    }

    //used by dashboard controller for latest inv
    public function routeToLatest()
    {
        $inventory = Inventory::orderby('created_at', 'desc')->where('store_id', $this->store->id)->get()->first();
        return $this->countSummary($inventory);
    }

    //further details of a single category wihtin the inventory display
    public function countDive(Inventory $inventory, $category)
    {
        //get current categories
        $pc = new ProductController();

        $categories = $pc->buildCategories();

        //guard against poor category value passed
        if (!in_array($category, array_keys($categories))) {
            return redirect()->route('inventory.view');
        }

        //pull only those products related to category
        $productMappings = $inventory->products()
            ->where("category", $category)
            ->orderby('subcategory', 'desc')
            ->with("units")
            ->get();

        $totalValue = 0;
        $totalQuantity = 0;
        $catSummary = [];

        //why is this not just calling full calc?
        //map sub category information
        foreach ($productMappings as $product) {
            //total cases and quantities
            $totalQuantity += ($product->pivot->quantity / $product->units->quantity);
            $totalValue += ($product->pivot->quantity / $product->units->quantity) * $product->units->price;

            //produce a map between subcategory quantitys and sums based on product data
            if (isset($catSummary[$product->subcategory])) {
                $catSummary[$product->subcategory]["quantity"] += $product->pivot->quantity / $product->units->quantity;
                $catSummary[$product->subcategory]["sum"] += ($product->pivot->quantity / $product->units->quantity) * $product->units->price;
            } else {
                $catSummary[$product->subcategory]["quantity"] = $product->pivot->quantity / $product->units->quantity;
                $catSummary[$product->subcategory]["sum"] = ($product->pivot->quantity / $product->units->quantity) * $product->units->price;
            }
        }

        //pull chart data with category summation
        [$chartData1, $chartData2] = $this->gatherChartData($catSummary);


        $title = "Count Summary";

        return view("inventory.summary-category", [
            "title" => $title,
            "category" => $category,
            "products" => $productMappings,
            "sum" => $totalValue,
            "quantity" => $totalQuantity,
            "store" => $this->store,
            "inventory" => $inventory,
            "chartData1" => $chartData1,
            "chartData2" => $chartData2
        ]);
    }

    //produce total cases, quantity and data map to categories for reporting
    public function fullCalc($productMappings)
    {

        $totalValue = 0;
        $totalQuantity = 0;
        $catSummary = [];

        //loop through and sum
        foreach ($productMappings as $product) {
            //quantiy may need to change depending on if we are discussing cases or indivdual units
            if (isset($catSummary[$product->category])) {
                $catSummary[$product->category]["quantity"] += $product->pivot->quantity / $product->units->quantity;
                $catSummary[$product->category]["sum"] += ($product->pivot->quantity / $product->units->quantity) * $product->units->price;
            } else {
                $catSummary[$product->category]["quantity"] = $product->pivot->quantity / $product->units->quantity;
                $catSummary[$product->category]["sum"] = ($product->pivot->quantity / $product->units->quantity) * $product->units->price;
            }

            $totalQuantity += ($product->pivot->quantity / $product->units->quantity);
            $totalValue += ($product->pivot->quantity / $product->units->quantity) * $product->units->price;
        }

        return [$catSummary, $totalQuantity, $totalValue];
    }

    //pull data for the inventory print out page using print styles
    public function print(Inventory $inventory)
    {
        $productMappings = $inventory->products()->orderby('category', 'asc')->with("units")->get();
        [$catSummary, $totalQuantity, $totalValue] = $this->fullCalc($productMappings);

        $title = "Print Count";

        return view("inventory.summary-print", [
            "title" => $title,
            "catSummary" => $catSummary,
            "sum" => $totalValue,
            "quantity" => $totalQuantity,
            "store" => $this->store,
            "inventory" => $inventory
        ]);
    }

    //pull searchable data view for all current inventories, allow for the showing of deletion messages
    public function view(Request $request, $response = "")
    {
        $title = "View Stock Counts";
        $inventory = new Inventory;
        //get searchables
        $fields = $inventory->getSearchable();
        //remember to pass the search fields as a mapping of table to fields
        $searchFields["inventories"] = $fields;

        //setup search vars
        $search = $request->search;
        $sort = $request->sort;
        $sortDirection = "desc";

        if ($sort == null) {
            $sort = "id";
        }

        //use v4 to restrict and only allow for showing of inventories related to the indivdual store, while still allowing for the full searchable features to be present
        $modelSearch = new ModelSearchv4(Inventory::class, $searchFields, $searchFields, ["table" => "inventories", "field" => "store_id", "value" => $this->store->id]);
        $inventory = $modelSearch->search($search, $sort, $sortDirection);
        //dd($inventory);

        return view("inventory.view", [
            "title" => $title,
            "inventory" => $inventory,
            "searchFields" => $fields,
            "search" => $search,
            "sort" => $sort,
            "response" => $response
        ]);
    }

    //delete a passed inventory, passing this way prevents issues of passing an id, as if its invalid it cannot be passed
    public function destroy(Inventory $inventory, Request $request)
    {
        $response = "Successfully deleted Inventory count #" . $inventory->id;
        $inventory->delete();
        return $this->view($request, $response);
    }
}
