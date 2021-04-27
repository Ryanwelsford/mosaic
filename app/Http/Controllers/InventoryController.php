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

class InventoryController extends UserAccessController
{
    public function home()
    {

        $title = "Inventory Home";
        //pull latest count for this store
        $inventory = Inventory::orderby('created_at', 'desc')->where('store_id', $this->store->id)->get()->first();

        $menuitems = [
            ["title" => "New Count", "anchor" => route("inventory.new"), "img" => "/images/icons/new-256.png"],
            ["title" => "Current Count", "anchor" => route("inventory.summary", [$inventory->id]), "img" => "/images/icons/summary-256.png"],
            ["title" => "View Count", "anchor" => route("inventory.view"), "img" => "/images/icons/view-256.png"],
            ["title" => "Inventory Reports", "anchor" => "/test", "img" => "/images/icons/report-256.png"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
        ]);
    }
    //change this to display all pack/each details as well
    public function store(Request $request)
    {
        $title = "Inventory Count";
        $today = new Carbon();
        $products = Product::orderby('category')->orderby('subcategory')->orderby('name')->with("units")->get();
        $firstCategory = "Chilled";

        $mappedProducts = [];

        $modelValidator = new ModelValidator(Inventory::class, $request->id, old());
        $inventory = $modelValidator->validate();

        //i.e. edit requested
        if (isset($request->id) && $inventory != false) {
            $inventory = Inventory::find($request->id);
            $enteredProducts = $inventory->products()->get();

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

    //will need to account for saved and booked versions
    public function save(Request $request)
    {

        $productsToQuantity = $request->product;
        $status = $request->status;
        $inventory = new Inventory;

        if (isset($request->id)) {
            $inventory = Inventory::find($request->id);
        }

        $inventory->fillitem($request->id, $this->store->id, $status);

        $inventory->save();

        $organisedMappings = [];
        foreach ($productsToQuantity as $product_id => $quantity) {
            //fix for empty input fields, solves issue of form not filling with 0s properly
            if (is_null($quantity)) {
                $quantity = 0;
            }
            $organisedMappings[$product_id] = ["quantity" => $quantity];
        }

        $inventory->products()->sync($organisedMappings);

        return $this->confirm($inventory);
    }

    public function confirm(Inventory $inventory)
    {
        //dd($inventory);
        $title = "Count Confirmation";
        [$catSummary, $quantity, $sum] = $this->fullCalc($inventory->products()->orderby('category', 'asc')->with("units")->get());

        $heading = "Count Successfully " . $inventory->status;
        $text = "Count has been created successfully for a total value of £" . number_format($sum, 2) . " and " . $quantity . " cases in total";
        $anchor = route('inventory.summary', [$inventory->id]);
        $anchorText = " to view the count summary";
        return view("general.confirmation-custom", ["title" => $title, "heading" => $heading, "text" => $text, "anchor" => $anchor, "anchorText" => $anchorText]);
    }

    public function countSummary(Inventory $inventory)
    {
        $productMappings = $inventory->products()->orderby('category', 'asc')->with("units")->get();

        [$catSummary, $totalQuantity, $totalValue] = $this->fullCalc($productMappings);


        $title = "Count Summary";

        return view("inventory.summary", [
            "title" => $title,
            "catSummary" => $catSummary,
            "sum" => $totalValue,
            "quantity" => $totalQuantity,
            "store" => $this->store,
            "inventory" => $inventory
        ]);
    }

    public function routeToLatest() {
        $inventory = Inventory::orderby('created_at', 'desc')->where('store_id', $this->store->id)->get()->first();
        return $this->countSummary($inventory);
    }

    public function countDive(Inventory $inventory, $category)
    {
        $pc = new ProductController();

        $categories = $pc->buildCategories();

        //guard against poor category value passed
        if (!in_array($category, array_keys($categories))) {
            return redirect()->route('inventory.view');
        }

        $productMappings = $inventory->products()->where("category", $category)->orderby('subcategory', 'desc')->with("units")->get();

        $totalValue = 0;
        $totalQuantity = 0;

        foreach ($productMappings as $product) {
            $totalQuantity += ($product->pivot->quantity / $product->units->quantity);
            $totalValue += ($product->pivot->quantity / $product->units->quantity) * $product->units->price;
        }

        $title = "Count Summary";

        return view("inventory.summary-category", [
            "title" => $title,
            "category" => $category,
            "products" => $productMappings,
            "sum" => $totalValue,
            "quantity" => $totalQuantity,
            "store" => $this->store,
            "inventory" => $inventory
        ]);
    }

    public function fullCalc($productMappings)
    {

        $totalValue = 0;
        $totalQuantity = 0;
        $catSummary = [];

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

    public function view(Request $request, $response = "")
    {
        $title = "View Stock Counts";
        $inventory = new Inventory;
        $fields = $inventory->getSearchable();
        //remember to pass the search fields as a mapping of table to fields
        $searchFields["inventories"] = $fields;
        $search = $request->search;
        $sort = $request->sort;
        $sortDirection = "desc";

        if ($sort == null) {
            $sort = "id";
        }

        //so v3 does work with a passed restriction
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

    //should be saved only no deleting booked
    public function destroy(Inventory $inventory, Request $request)
    {
        $response = "Successfully deleted Inventory count #" . $inventory->id;
        $inventory->delete();
        return $this->view($request, $response);
    }
}
