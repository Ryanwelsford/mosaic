<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Types\UserAccessController;

class StockOnHandController extends UserAccessController
{
    public function home()
    {

        $title = "Stock on Hand Home";

        $menuitems = [
            ["title" => "New Count", "anchor" => route('soh.new'), "img" => "/images/icons/new-256.png"],
            ["title" => "Adjust Products", "anchor" => route('soh.assign'), "img" => "/images/icons/edit-256.png"],
            ["title" => "Edit Count", "anchor" => "/test", "img" => "/images/icons/edit-256.png"],
            ["title" => "View Counts", "anchor" => "/test", "img" => "/images/icons/view-256.png"],
            ["title" => "Stock on Hand Reports", "anchor" => "/test", "img" => "/images/icons/report-256.png"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
        ]);
    }

    //count form will need to pull assigned store products
    //TODO tools to nothing atm
    public function store()
    {
        $title = "New Count";
        //adjust query to find correct products
        $products = $this->store->products()->orderby('category')->orderby('subcategory')->orderby('name')->get();


        return view('soh.new', ["title" => $title, "products" => $products]);
    }

    public function saveCount(Request $request)
    {
        return view('general.confirmation-custom', [
            "title" => "Confirmation",
            "heading" => "Count Success",
            "text" => "Count input successfully",
            "anchorText" => "to view stock on hand reports",
            "anchor" => "#"
        ]);
    }

    //display assign form to select products for store
    public function assign()
    {
        $title = "Assign SOH Products";

        $assignedProducts = $this->store->products()->get();
        $assignedmap = [];
        foreach ($assignedProducts as $product) {
            $assignedmap[$product->id] = $product;
        }

        $productList = Product::orderby('category')->orderby('subcategory')->orderby('name')->get();

        return view("soh.assign", [
            "title" => $title,
            "productList" => $productList,
            "assignedMap" => $assignedmap
        ]);
    }

    public function saveAssigned(Request $request)
    {
        //no validation required i suppose, maybe check at least 1 product?

        $this->validate(
            $request,
            [
                'sohList' => ['required']
            ],
            [
                "sohList.required" => "At least one product must be selected to save"
            ]
        );
        $sohList = $request->sohList;

        $this->store->products()->sync($sohList);
        //confirm

        return view("general.confirmation-custom", [
            "heading" => "Stock on Hand Updated",
            "text" => "Successfully updated products",
            "anchor" => route("soh.new"),
            "anchorText" => "to complete a new stock on hand count",
            "title" => "Confirmation"
        ]);
    }
    public function confirm()
    {
    }
    public function view()
    {
    }
    //delete count
    public function destroy()
    {
    }
}
