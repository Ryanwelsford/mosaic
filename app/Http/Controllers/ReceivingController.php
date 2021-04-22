<?php

namespace App\Http\Controllers;

use stdClass;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Helpers\ModelSearchv3;
use App\Http\Controllers\Types\UserAccessController;

class ReceivingController extends UserAccessController
{
    public function home()
    {

        $title = "Receiving Home";

        $menuitems = [
            ["title" => "New Receipt", "anchor" => route("receiving.new"), "img" => "/images/icons/new-256.png"],
            ["title" => "Edit receipt", "anchor" => route("receiving.view"), "img" => "/images/icons/edit-256.png"],
            ["title" => "View receipts", "anchor" => route("receiving.view"), "img" => "/images/icons/view-256.png"],
            ["title" => "Receipt Reports", "anchor" => "/test", "img" => "/images/icons/report-256.png"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
        ]);
    }

    //TODO make edit work and refill form
    //select order for reciept to be based on
    public function store()
    {
        $title = "New Reciept";
        $store = $this->user->stores()->get()->first();
        $today = new Carbon();
        $menus = Menu::orderby("updated_at")->get();

        return view('receipt.new', ["title" => $title, "today" => $today, "menus" => $menus]);
    }

    //TODO find open receipts
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

        $receipt = new Receipt;

        if (isset($request->id)) {
            $receipt = Receipt::find($request->id);
        }

        $productMappings = $request->product;

        $organisedMappings = [];
        $store = $this->user->stores()->get()->first();

        foreach ($productMappings as $product_id => $quantity) {
            //fix for empty input fields, solves issue of form not filling with 0s properly
            //prevent 0 quantities being entered for each product in menu
            $required = true;
            if (is_null($quantity) || $quantity == 0) {
                $required = false;
            }
            if ($required) {
                $organisedMappings[$product_id] = ["quantity" => $quantity];
            }
        }

        //save receipt
        $receipt->fillItem($request->id, $request->date, $request->reference, $store->id);
        $receipt->save();
        //map products to receipts.
        $receipt->products()->sync($organisedMappings);

        return $this->confirm($receipt);
    }
    //display list of created reciepts with view, delete, print options
    public function view(Request $request, $response = "")
    {
        $title = "View Receipts";
        $receipts = new Receipt;

        $fields = $receipts->getSearchable();
        //remember to pass the search fields as a mapping of table to fields
        $searchFields["receipts"] = $fields;
        $search = $request->search;
        $sort = $request->sort;

        if ($sort == null) {
            $sort = "date";
        }
        $store = $this->user->stores()->get()->first();

        //so v3 does work with a passed restriction
        $modelSearch = new ModelSearchv3(Receipt::class, $searchFields, ["table" => "receipts", "field" => "store_id", "value" => $store->id]);
        $receipts = $modelSearch->search($search, $sort, "desc");

        return view("receipt.view", [
            "title" => $title,
            "receipts" => $receipts,
            "searchFields" => $fields,
            "search" => $search,
            "sort" => $sort,
            "response" => $response
        ]);
    }
    public function confirm(Receipt $receipt)
    {

        $values = $receipt->products()->with("units")->get();
        [$sum, $quantity] = $this->calc($values);

        $title = "Receipt Confirmation";
        $heading = "Receipt Successfully Booked";
        $text = "Receipt has been created successfully for a total value of £" . number_format($sum, 2) . " and " . $quantity . " cases in total";
        $anchor = route('receiving.new');
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

    //single reciept summary in printable
    public function print(Receipt $receipt)
    {
        [$listing, $store, $sum, $quantity] = $this->receiptDetails($receipt);

        $title = "Receipt Summary";

        return view("receipt.print", [
            "title" => $title,
            "listing" => $listing,
            "receipt" => $receipt,
            "store" => $store,
            "sum" => $sum,
            "quantity" => $quantity
        ]);
    }

    //view a single reciept summary
    public function summary(Receipt $receipt)
    {

        [$listing, $store, $sum, $quantity] = $this->receiptDetails($receipt);

        $title = "Receipt Summary";

        return view("receipt.summary", [
            "title" => $title,
            "listing" => $listing,
            "receipt" => $receipt,
            "store" => $store,
            "sum" => $sum,
            "quantity" => $quantity
        ]);
    }

    private function receiptDetails(Receipt $receipt)
    {

        $listing = $receipt->products()->with("units")->get();

        $store = $receipt->store()->get()->first();
        $newListing = [];
        foreach ($listing as $each) {
            if ($each->pivot->quantity > 0) {
                $newListing[] = $each;
            }
        }

        $listing = $newListing;
        [$sum, $quantity] = $this->calc($listing);

        return [$listing, $store, $sum, $quantity];
    }
    //delete reciept
    public function destroy(Receipt $receipt, Request $request)
    {
        $response = "Sucessfully deleted order #" . $receipt->id;
        $receipt->delete();
        return $this->view($request, $response);
    }
}
