<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Http\Helpers\ModelValidator;
use App\Http\Controllers\Types\UserAccessController;

class InventoryController extends UserAccessController
{
    public function home()
    {

        $title = "Inventory Home";

        $menuitems = [
            ["title" => "New Count", "anchor" => route("inventory.new"), "img" => "/images/icons/new-256.png"],
            ["title" => "Edit Count", "anchor" => "/test", "img" => "/images/icons/edit-256.png"],
            ["title" => "View Count", "anchor" => "/test", "img" => "/images/icons/view-256.png"],
            ["title" => "Inventory Reports", "anchor" => "/test", "img" => "/images/icons/report-256.png"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
        ]);
    }

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
    }
}
