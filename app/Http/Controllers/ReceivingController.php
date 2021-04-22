<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Types\UserAccessController;
use stdClass;

class ReceivingController extends UserAccessController
{
    public function home()
    {

        $title = "Receiving Home";

        $menuitems = [
            ["title" => "New Receipt", "anchor" => route("receiving.new"), "img" => "/images/icons/new-256.png"],
            ["title" => "Edit receipt", "anchor" => "/test", "img" => "/images/icons/edit-256.png"],
            ["title" => "View receipts", "anchor" => "/test", "img" => "/images/icons/view-256.png"],
            ["title" => "Receipt Reports", "anchor" => "/test", "img" => "/images/icons/report-256.png"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
        ]);
    }

    //TODO
    //select order for reciept to be based on
    public function store()
    {
        $title = "New Reciept";
        $store = $this->user->stores()->get()->first();
        $today = new Carbon();
        $menus = Menu::orderby("updated_at")->get();

        return view('receipt.new', ["title" => $title, "today" => $today, "menus" => $menus]);
    }

    public function select(Request $request)
    {
        $title = "Select Products";
        $origin = 'value="0"';
        $receipt = new stdClass();
        $receipt->id = $request->id;
        $receipt->date = $request->date;
        $receipt->reference = $request->reference;

        //validate previous form
        $this->validate(
            $request,
            [
                'reference' => ['required']
            ],
            [
                "reference.required" => "A reference must be entered"
            ]
        );

        $display = $request->display_mode;
        $view = "receipt.assign";
        if ($display == true) {
            $view = "receipt.full";
        }

        $menu = Menu::where("id", $request->menu_id)->get()->first();

        //if order exists pull its mappings, else pull the menu products listing
        /*
        if (isset($orderDetails['id'])) {
            $order = Order::where('id', $orderDetails['id'])->get()->first();
            $products = $order->products()->orderby('category')->orderby('subcategory')->orderby('name')->with("units")->get();
        } else {
            $products = $menu->products()->orderby('category')->orderby('subcategory')->orderby('name')->with("units")->get();
        }*/

        $products = $menu->products()->orderby('category')->orderby('subcategory')->orderby('name')->with("units")->get();

        $organisedProducts = [];
        foreach ($products as $product) {
            $organisedProducts[$product->category][$product->subcategory][] = $product;
        }

        $store = $this->user->stores()->get()->first();
        return view($view, [
            "title" => $title,
            "menu" => $menu,
            "products" => $products,
            "organisedProducts" => $organisedProducts,
            "store" => $store,
            "origin" => $origin,
            "receipt" => $receipt
        ]);
    }
    //save a receipt to db
    public function save(Request $request)
    {
        //add a guard to prevent incorrect reciept id or something liek that

        //$receipt = new Receipt;
        if (isset($request->id)) {
            $order = Order::find($request->id);
        }

    }
    //display list of created reciepts with view,delete, print options
    public function view()
    {
    }

    //single reciept summary in printable
    public function print()
    {
    }

    //view a single reciept summary
    public function viewReciept()
    {
    }
    //delete reciept
    public function delete()
    {
    }
}
