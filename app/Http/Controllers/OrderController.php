<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Menu;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Helpers\ModelSearch;
use App\Http\Helpers\ModelSearchv3;
use App\Http\Helpers\ModelValidator;
use App\Http\Controllers\Types\UserAccessController;

class OrderController extends UserAccessController
{
    //$user is available as a protected variable of UserAccessController

    public function home()
    {

        $title = "Order Home";
        $viewRoute = route("order.view");
        $menuitems = [
            ["title" => "New Order", "anchor" => route("order.new"), "img" => "/images/icons/new-256.png"],
            ["title" => "New Automated Order", "anchor" => '#', "img" => "/images/icons/robot-256.png"],
            ["title" => "Edit Saved Order", "anchor" => route("order.view", ["search" => "Saved"]), "img" => "/images/icons/edit-256.png"],
            ["title" => "View All Orders", "anchor" => $viewRoute, "img" => "/images/icons/view-256.png"],
            ["title" => "Order Reports", "anchor" => "/test", "img" => "/images/icons/report-256.png"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title,
        ]);
    }

    public function store(Request $request)
    {
        $title = "Create An Order";

        //why does user have many stores...
        $store = $this->user->stores()->get()->first();
        $menus = Menu::orderby("updated_at")->get();

        $modelValidator = new ModelValidator(Order::class, $request->id, old());
        $order = $modelValidator->validate();
        $today = new Carbon();
        //hacky fix for order being in an array
        if (!empty(old())) {
            $order = (object) $order->order;
            $order->delivery_date = new Carbon($order->delivery_date);
        }

        if (!is_null($order) && empty(old())) {
            $order->delivery_date = new Carbon($order->delivery_date);

            if ($order->status == "Booked") {
                $order = null;
            }
        }


        return view("orders.new", [
            "title" => $title,
            "order" => $order,
            "user" => $this->user,
            "store" => $store,
            "menus" => $menus,
            "today" => $today
        ]);
    }

    public function pick(Request $request)
    {
        $title = "Select Products";
        $origin = 'value="0"';
        //validate previous form
        $this->validate(
            $request,
            [
                'order.reference' => ['required']
            ],
            [
                "order.reference.required" => "A reference must be entered"
            ]
        );

        $display = $request->display_mode;
        $view = "orders.pick";
        if ($display == true) {
            $view = "orders.full";
        }
        $orderDetails = $request->order;

        $menu = Menu::where("id", $request->order["menu_id"])->get()->first();

        //if order exists pull its mappings, else pull the menu products listing
        if (isset($orderDetails['id'])) {
            $order = Order::where('id', $orderDetails['id'])->get()->first();
            $products = $order->products()->orderby('category')->orderby('subcategory')->orderby('name')->with("units")->get();
        } else {
            $products = $menu->products()->orderby('category')->orderby('subcategory')->orderby('name')->with("units")->get();
        }

        //fill order to pass into view, do not save yet prevents half completed orders
        $order = new Order;
        $order->fillItemArray($orderDetails);

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
            "order" => $order,
            "store" => $store,
            "origin" => $origin
        ]);
    }

    public function save(Request $request)
    {
        $status = "Booked";

        if (isset($request->save)) {
            $status = "Saved";
        }


        $order = new Order;

        //i.e. edit
        if (isset($request->id)) {
            $order = Order::find($request->id);
        }

        //reorganise form outputs to have a mapping of product_ids to "quantity" => entered value
        $productMappings = $request->product;

        $organisedMappings = [];
        foreach ($productMappings as $product_id => $quantity) {
            //fix for empty input fields, solves issue of form not filling with 0s properly
            if (is_null($quantity)) {
                $quantity = 0;
            }
            $organisedMappings[$product_id] = ["quantity" => $quantity];
        }

        //create the order and save it
        $order->fillItem($request->id, $request->delivery_date, $status, $request->reference, $request->menu_id, $request->store_id);
        $order->Save();

        //save pivot table mappings
        $order->products()->sync($organisedMappings);

        //add email confirmation if booked

        //confirm message

        return $this->confirm($order);
    }

    public function confirm(Order $order)
    {
        if ($order->status == "book") {
            $status = "Booked";
        } else {
            $status = "Saved";
        }

        $values = $order->products()->with("units")->get();

        $quantity = 0;
        $sum = 0;

        foreach ($values as $each) {
            $quantity += $each->pivot->quantity;
            $sum += $each->pivot->quantity * $each->units->price;
        }

        $title = "Order Confirmation";
        $heading = "Order Successfully " . $status;
        $text = "Order has been created successfully for a total value of £" . number_format($sum, 2) . " and " . $quantity . " cases in total";
        $anchor = route('order.new');
        return view("general.confirmation-print", ["title" => $title, "heading" => $heading, "text" => $text, "anchor" => $anchor]);
    }

    //TODO add pagination ability to queries either through larvel pagination or not
    public function view(Request $request, $response = "")
    {
        $title = "View Orders";
        $orders = new Order;

        $fields = $orders->getSearchable();
        //remember to pass the search fields as a mapping of table to fields
        $searchFields["orders"] = $fields;
        $search = $request->search;
        $sort = $request->sort;
        if ($sort == null) {
            $sort = "delivery_date";
        }
        $store = $this->user->stores()->get()->first();

        //so v3 does work with a passed restriction
        $modelSearch = new ModelSearchv3(Order::class, $searchFields, ["table" => "orders", "field" => "store_id", "value" => $store->id]);
        $orders = $modelSearch->search($search, $sort, "desc");

        return view("orders.view", [
            "title" => $title,
            "orders" => $orders,
            "searchFields" => $fields,
            "search" => $search,
            "sort" => $sort,
            "response" => $response
        ]);
    }
    //TODO add guards for order id
    public function summary(Request $request)
    {
        $title = "Order Summary";
        [$order, $store, $menu, $listing, $sum, $quantity] = $this->orderDetails($request);

        return view("orders.summary", [
            "title" => $title,
            "listing" => $listing,
            "order" => $order,
            "store" => $store,
            "menu" => $menu,
            "sum" => $sum,
            "quantity" => $quantity
        ]);
    }

    public function destroy(Order $order, Request $request)
    {
        $response = "Sucessfully deleted order #" . $order->id;
        $order->delete();
        return $this->view($request, $response);
    }

    private function calc($values)
    {
        $quantity = 0;
        $sum = 0;
        foreach ($values as $each) {
            $quantity += $each->pivot->quantity;
            $sum += $each->pivot->quantity * $each->units->price;
        }

        return [$sum, $quantity];
    }

    private function orderDetails($request)
    {

        $id = $request->id;
        $order = Order::find($id);
        $store = $order->store()->get()->first();
        $menu = $order->menu()->get()[0];
        $listing = $order->products()->with("units")->get();

        $newListing = [];
        foreach ($listing as $each) {

            if ($each->pivot->quantity > 0) {
                $newListing[] = $each;
            }
        }

        $listing = $newListing;
        [$sum, $quantity] = $this->calc($listing);

        return [$order, $store, $menu, $listing, $sum, $quantity];
    }

    public function print(Request $request)
    {
        $title = "Order Printout";

        [$order, $store, $menu, $listing, $sum, $quantity] = $this->orderDetails($request);

        return view("orders.print", [
            "title" => $title,
            "listing" => $listing,
            "order" => $order,
            "store" => $store,
            "menu" => $menu,
            "sum" => $sum,
            "quantity" => $quantity
        ]);
    }
}
