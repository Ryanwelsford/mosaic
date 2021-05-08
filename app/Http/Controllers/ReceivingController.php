<?php

namespace App\Http\Controllers;

use stdClass;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Helpers\ModelSearchv3;
use App\Http\Helpers\ModelValidator;
use App\Http\Helpers\ModelSearch\ModelSearchv4;
use App\Http\Controllers\Types\UserAccessController;

class ReceivingController extends UserAccessController
{
    public function home()
    {

        $title = "Receiving Home";

        //add a report if possible?
        $menuitems = [
            ["title" => "New Receipt", "anchor" => route("receiving.new"), "img" => "/images/icons/new-256.png"],
            ["title" => "Edit receipt", "anchor" => route("receiving.view"), "img" => "/images/icons/edit-256.png"],
            ["title" => "View receipts", "anchor" => route("receiving.view"), "img" => "/images/icons/view-256.png"],
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
        ]);
    }


    //select order for reciept to be based on
    public function store(Request $request)
    {
        $title = "New Reciept";
        $store = $this->user->stores()->get()->first();
        $today = new Carbon();

        if (isset($request->id)) {
            $today = null;
        }
        $menus = Menu::where("status", "Active")->orderby("updated_at", 'desc')->get();

        $modelValidator = new ModelValidator(Receipt::class, $request->id, old());
        $receipt = $modelValidator->validate();


        //dd($receipt);
        return view('receipt.new', ["title" => $title, "today" => $today, "menus" => $menus, "receipt" => $receipt]);
    }


    public function select(Request $request)
    {
        $title = "Select Products";
        $origin = 'value="0"';

        //setup core receipt values
        $receipt = new stdClass();
        $receipt->id = $request->id;
        $receipt->date = $request->date;
        $receipt->reference = $request->reference;

        //validate previous form
        //dont need to validate date as it has a min of today
        $this->validate(
            $request,
            [
                'reference' => ['required']
            ],
            [
                "reference.required" => "A reference must be entered"
            ]
        );

        //TODO
        $view = "receipt.assign";

        //get menu product listings with the units required
        $menu = Menu::where("id", $request->menu_id)->get()->first();
        $products = $menu->products()->orderby('category')->orderby('subcategory')->orderby('name')->with("units")->get();

        //if edit
        if (isset($request->id)) {
            //get previous reciept
            $receipt = Receipt::where('id', $request->id)->get()->first();
            //get entered values
            $Enteredproducts = $receipt->products()->orderby('category')->orderby('subcategory')->orderby('name')->with("units")->get();

            //map into id -> product
            $map = [];
            foreach ($Enteredproducts as $product) {
                $map[$product->id] = $product;
            }
        }

        //re organise products based on category and subcateogry
        $organisedProducts = [];
        foreach ($products as $product) {

            //if product was entered previously change it to the previously found product
            if (isset($map[$product->id])) {
                $product = $map[$product->id];
            }

            //organise
            $organisedProducts[$product->category][$product->subcategory][] = $product;
        }


        return view($view, [
            "title" => $title,
            "menu" => $menu,
            "products" => $products,
            "organisedProducts" => $organisedProducts,
            "store" => $this->store,
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
        dd($productMappings);
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

        //setup search vars
        $search = $request->search;
        $sort = $request->sort;
        $sortDirection = "desc";

        if ($sort == null) {
            $sort = "id";
        }

        $store = $this->user->stores()->get()->first();

        //restrict to view only the logged stores receipts
        $modelSearch = new ModelSearchv4(Receipt::class, $searchFields, $searchFields, ["table" => "receipts", "field" => "store_id", "value" => $store->id]);
        $receipts = $modelSearch->search($search, $sort, $sortDirection);

        $today = Carbon::now();

        return view("receipt.view", [
            "title" => $title,
            "receipts" => $receipts,
            "searchFields" => $fields,
            "search" => $search,
            "sort" => $sort,
            "response" => $response,
            "today" => $today
        ]);
    }

    //confirm receipt creation
    public function confirm(Receipt $receipt)
    {

        $values = $receipt->products()->with("units")->get();
        [$sum, $quantity] = $this->calc($values);

        $title = "Receipt Confirmation";
        $heading = "Receipt Successfully Booked";
        $text = "Receipt has been created successfully for a total value of £" . number_format($sum, 2) . " and " . $quantity . " cases in total";
        $anchor = route('receiving.print', [$receipt->id]);
        return view("general.confirmation-print", ["title" => $title, "heading" => $heading, "text" => $text, "anchor" => $anchor]);
    }

    //calc reciept totals
    public function calc($values)
    {
        $quantity = 0;
        $sum = 0;

        foreach ($values as $each) {
            $quantity += $each->pivot->quantity;
            //doesnt need divide as receipts are case only
            $sum += $each->pivot->quantity * $each->units->price;
        }

        return [$sum, $quantity];
    }

    //single reciept summary in printable
    public function print(Receipt $receipt)
    {
        //gather receipt information
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
        //gather receipt information
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

    //full receipt infomration
    private function receiptDetails(Receipt $receipt)
    {
        //get associated products
        $listing = $receipt->products()->with("units")->get();

        //map receipt data to new array
        $store = $receipt->store()->get()->first();
        $newListing = [];
        foreach ($listing as $each) {
            if ($each->pivot->quantity > 0) {
                $newListing[] = $each;
            }
        }

        //get total information of reciept
        $listing = $newListing;
        [$sum, $quantity] = $this->calc($listing);

        return [$listing, $store, $sum, $quantity];
    }
    //delete reciept
    public function destroy(Receipt $receipt, Request $request)
    {
        $response = "Sucessfully deleted receipt #" . $receipt->id;
        $receipt->delete();
        return $this->view($request, $response);
    }
}
