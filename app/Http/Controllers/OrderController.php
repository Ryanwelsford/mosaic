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
use App\Http\Helpers\ModelSearch\ModelSearchv4;
use App\Http\Controllers\Types\UserAccessController;

//TODO
//there are a few errors here
//firstly sometimes edited an order cause the order to be empty
//editing a saved order occasionally prevents a menu change being allowed (imagine if someone put the incorrect menu in and tried to edit, they cannot change the menu as it just takes the previously entered menu.
//thirdly refilling the order form needs changing see work in receipt controller.

class OrderController extends UserAccessController
{
    //$user is available as a protected variable of UserAccessController

    public function home()
    {

        $title = "Order Home";
        $viewRoute = route("order.view");
        $menuitems = [
            ["title" => "New Order", "anchor" => route("order.new"), "img" => "/images/icons/new-256.png", "action" => "Create"],
            //["title" => "New Automated Order", "anchor" => '#', "img" => "/images/icons/robot-256.png"],
            ["title" => "Edit Saved Order", "anchor" => route("order.view", ["search" => "Saved"]), "img" => "/images/icons/edit-256.png", "action" => "Edit"],
            ["title" => "View All Orders", "anchor" => $viewRoute, "img" => "/images/icons/view-256.png", "action" => "View"],
            ["title" => "Weekly Order Summary", "anchor" => route('order.weekSelect'), "img" => "/images/icons/report-256.png"],
            ["title" => "Monthly Order Details", "anchor" => route('order.weekSelect', ["month" => "true"]), "img" => "/images/icons/report-256.png"],
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
        $store = $this->store;
        //restrict possible orders to only active menus
        $menus = Menu::orderby("updated_at", 'desc')->where('status', 'Active')->get();

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

        //reorganise products into an array mapping category and subcategory to prd allows for looping and organising by product in tabs
        $organisedProducts = [];
        foreach ($products as $product) {
            $organisedProducts[$product->category][$product->subcategory][] = $product;
        }

        $store = $this->user->stores()->get()->first();

        return view("orders.pick", [
            "title" => $title,
            "menu" => $menu,
            "products" => $products,
            "organisedProducts" => $organisedProducts,
            "order" => $order,
            "store" => $store,
            "origin" => $origin
        ]);
    }

    //save/book order entry
    public function save(Request $request)
    {
        $status = "Booked";

        //dependant on if save or book button clicked
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
        [$sum, $quantity] = $this->calc($order->products()->with("units")->get());

        //slightly hacky way to add total in
        $order->saveTotal($sum);
        $order->Save();
        //add email confirmation if booked

        //confirm message

        return $this->confirm($order, $sum, $quantity);
    }

    //display confirmation of order creation with sum and quantity data
    public function confirm(Order $order, $sum, $quantity)
    {
        if ($order->status == "book") {
            $status = "Booked";
        } else {
            $status = "Saved";
        }

        $title = "Order Confirmation";
        $heading = "Order Successfully " . $status;
        $text = "Order has been created successfully for a total value of ??" . number_format($sum, 2) . " and " . $quantity . " cases in total";

        $anchor = route('order.print', ['id' => $order->id]);

        return view("general.confirmation-print", ["title" => $title, "heading" => $heading, "text" => $text, "anchor" => $anchor]);
    }

    //display table structure for orders, with pagination and full search/sort features
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
        $modelSearch = new ModelSearchv4(Order::class, $searchFields, $searchFields, ["table" => "orders", "field" => "store_id", "value" => $store->id]);
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

    //display order summary page
    public function summary(Request $request)
    {

        //guard against malformed orders
        if (!is_null($this->orderGuard($request->id))) {
            return $this->orderGuard($request->id);
        }

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

    //delete unbooked order from system
    public function destroy(Order $order, Request $request)
    {
        $response = "Sucessfully deleted order #" . $order->id;
        $order->delete();
        return $this->view($request, $response);
    }

    //calc sum and quantity only
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

    //sum up order information into a category map and sum/quantity totals
    private function fullCalc($values)
    {
        $quantity = 0;
        $sum = 0;
        $catSummary = [];
        foreach ($values as $each) {
            $quantity += $each->pivot->quantity;
            $sum += $each->pivot->quantity * $each->units->price;

            if (!isset($catSummary[$each->category])) {
                $catSummary[$each->category] = 0;
            }

            $catSummary[$each->category] += ($each->pivot->quantity * $each->units->price);
        }

        return [$sum, $quantity, $catSummary];
    }

    //based on order get relevent product listings
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

    //allow for printout of order information
    public function print(Request $request)
    {
        if (!is_null($this->orderGuard($request->id))) {
            return $this->orderGuard($request->id);
        }

        $title = "Order Printout";

        //gather details and pass to printable view
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

    //prevent access to order when incorrectly passed
    private function orderGuard($id)
    {
        $order = Order::find($id);

        if (!isset($id) || is_null($order)) {
            return redirect()->route('order.view');
        }
    }

    //summaries weekly orders for charting
    public function weeklyOrder(Request $request)
    {
        //use forecasting controller for some date checking functionality
        $fc = new ForecastingController();
        //create current date or passed date
        if (isset($request->date) && $fc->checkIsAValidDate($request->date)) {
            $startofweek = Carbon::parse($request->date)->startOfWeek();
            $endofweek = Carbon::parse($request->date)->endOfWeek();
        } else {
            //if date field is not set instead return the current weeks information
            $startofweek = Carbon::now()->startOfWeek();
            $endofweek = Carbon::now()->endOfWeek();
        }


        $title = "Weekly Order Summary";
        //gather this weeks orders
        $orders = $this->pullOrders($startofweek, $endofweek);

        $sum = 0;
        $quantity = 0;
        $catSummary = [];
        //map order sums and quantities for each order
        foreach ($orders as $order) {
            [$oSum, $oQuantity, $summary] = $this->fullCalc($order->products()->with("units")->get());
            $sum += $oSum;
            $quantity += $oQuantity;

            //use summary information to map category to value
            foreach ($summary as $category => $value) {
                if (!isset($catSummary[$category])) {
                    $catSummary[$category] = 0;
                }
                $catSummary[$category] += $value;
            }
        }
        $chartData = $this->chartData($catSummary);

        return view("orders.weeklySummary", [
            "title" => $title,
            "orders" => $orders,
            "startDate" => $startofweek,
            "endDate" => $endofweek,
            "sum" => $sum,
            "quantity" => $quantity,
            "chartData" => $chartData
        ]);
    }

    //get order details based on input dates
    public function pullOrders($startDate, $endDate)
    {

        $orders = Order::where("store_id", $this->store->id)
            ->where("status", "Booked")
            ->whereBetween("delivery_date", [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderby("delivery_date", 'asc')
            ->get();

        return $orders;
    }

    //map category to toal value of said category
    public function chartData($catSummary)
    {
        $data = [];
        $data[] = ['Category', 'Sum'];
        foreach ($catSummary as $category => $value) {
            $data[] = [$category, $value];
        }

        return json_encode($data);
    }

    //date selection tools same view different wording and route
    public function weekSelect(Request $request)
    {

        if (isset($request->month)) {
            $action = route('order.monthly');
            $word = "Month";
        } else {
            $action = route('order.weekly');
            $word = "Week";
        }

        $title = "Select " . $word . " to View";
        $heading = "Select " . $word;
        $message = "Pick a " . strtolower($word) . " to view order details";

        return view("general.weekSelect", ["title" => $title, "action" => $action, "heading" => $heading, "message" => $message]);
    }

    //pull and map data for a monthly summary of orders
    public function monthlySummary(Request $request)
    {
        $fc = new ForecastingController();
        //create current date or passed date
        if (isset($request->date) && $fc->checkIsAValidDate($request->date)) {
            $startofmonth = Carbon::parse($request->date)->startOfMonth();
            $endofmonth = Carbon::parse($request->date)->endOfMonth();
        } else {
            $startofmonth = Carbon::now()->startOfMonth();
            $endofmonth = Carbon::now()->endOfMonth();
        }


        $title = "Monthly Order Details";
        $orders = $this->pullOrders($startofmonth, $endofmonth);

        //setup base data
        $sum = 0;
        $chartData1 = [];
        $chartData1[] = ["Date", "Value"];
        $catSum = [];
        //step through orders
        foreach ($orders as $order) {
            $sum += $order->total;
            //map delivery date to order value for each order
            $chartData1[] = [$order->getDeliveryDate()->format('d-m-y'), $order->total];

            //pull product and quantity for each order
            $products = $order->products()->with("units")->get();
            foreach ($products as $product) {
                //if order value isnt set yet set to 0
                if (!isset($catSum[$order->getDeliveryDate()->format('d-m-y')][$product->category])) {
                    $catSum[$order->getDeliveryDate()->format('d-m-y')][$product->category] = 0;
                }
                //map order date to category totals
                $catSum[$order->getDeliveryDate()->format('d-m-y')][$product->category] += ($product->units->price * $product->pivot->quantity);
            }
        }

        //chart data for google charts
        $chartData2 = $this->chartData2($catSum);
        $chartData1 = json_encode($chartData1);

        return view("orders.monthly", [
            "title" => $title,
            "startDate" => $startofmonth,
            "endDate" => $endofmonth,
            "totalValue" => $sum,
            "orders" => $orders,
            "chartData1" => $chartData1,
            "chartData2" => $chartData2
        ]);
    }

    //so chart data based on category for display with order summaries
    public function chartData2($catSum)
    {
        //dd($catSum);
        $chartData2 = [];
        //pull category information
        $chartData2[] = ["Date", "Chilled", "Dry", "Frozen", "Other"];
        $pc = new ProductController;
        $categories = $pc->buildCategories();


        foreach ($catSum as $date => $category) {
            //set base date for column
            $stringArray = [$date];

            //loop through categories, allows for zeros to be set in otherwise empty category in order
            foreach ($categories as $name => $array) {

                //if category exists in order map to value otherwise 0
                if (isset($catSum[$date][$name])) {
                    $value = $catSum[$date][$name];
                } else {
                    $value = 0;
                }
                //build array up for entire order
                $stringArray[] = $value;
            }

            //add order details to main chart array
            $chartData2[] = $stringArray;
        }

        //has to be in json format for the javascript usage
        return json_encode($chartData2);
    }
}
