<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\StockOnHand;
use Illuminate\Http\Request;
use App\Http\Helpers\ModelSearchv3;
use App\Http\Helpers\ModelValidator;
use App\Http\Helpers\ModelSearch\ModelSearchv4;
use App\Http\Controllers\Types\UserAccessController;

//maybe add a reference to SOH table ?
class StockOnHandController extends UserAccessController
{
    public function home()
    {

        $title = "Stock on Hand Home";

        $menuitems = [
            ["title" => "New Count", "anchor" => route('soh.new'), "img" => "/images/icons/new-256.png", "action" => "Create"],
            ["title" => "Adjust Products", "anchor" => route('soh.assign'), "img" => "/images/icons/edit-256.png", "action" => "edit"],
            ["title" => "View Counts", "anchor" => route('soh.view'), "img" => "/images/icons/view-256.png", "action" => "view"],
            ["title" => "Weekly Summary", "anchor" => route('soh.date'), "img" => "/images/icons/report-256.png"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
        ]);
    }

    //count form will need to pull assigned store products
    //TODO tools to nothing atm
    public function store(Request $request)
    {
        $title = "New Count";
        $mappedProducts = [];
        $today = new Carbon();

        //if edit
        $modelValidator = new ModelValidator(StockOnHand::class, $request->id, old());
        $soh = $modelValidator->validate();

        $old = old();

        //remap products based on submissions
        if (isset($old['product'])) {
            $mappedProducts = $old['product'];
        } else if (isset($request->id)) {
            $entered = $soh->products()->get();
            foreach ($entered as $product) {
                $mappedProducts[$product->id] = $product->pivot->count;
            }
        }
        //adjust query to find correct products
        $products = $this->store->products()->orderby('category')->orderby('subcategory')->orderby('name')->get();

        return view('soh.new', [
            "title" => $title,
            "products" => $products,
            "soh" => $soh,
            "mappedProducts" => $mappedProducts,
            "store" => $this->store,
            "today" => $today
        ]);
    }

    public function saveCount(Request $request)
    {
        $this->validate(
            $request,
            [
                'reference' => ['required'],
            ],
            [
                "reference.required" => "A reference must be entered to book"
            ]
        );

        $counts = $request->product;
        $remappedCounts = [];

        foreach ($counts as $key => $count) {
            $remappedCounts[$key] = ["count" => $count];
        }

        $soh = new StockOnHand;

        if (isset($request->id)) {
            $soh = StockOnHand::find($request->id);
        }

        $soh->fillItem($request->id, $this->store->id, $request->reference);
        $soh->save();

        $soh->products()->sync($remappedCounts);

        return view('general.confirmation-custom', [
            "title" => "Confirmation",
            "heading" => "Count Success",
            "text" => "Count input successfully",
            "anchorText" => "to view the weekly totals",
            "anchor" => route("soh.weekly", ["date" => $soh->created_at->format('Y-m-d')])
        ]);
    }

    //display assign form to select products for store
    public function assign()
    {
        $title = "Assign SOH Products";

        $productController = new ProductController();
        $categories = $productController->buildCategories();

        $assignedProducts = $this->store->products()->get();
        $assignedmap = [];
        foreach ($assignedProducts as $product) {
            $assignedmap[$product->id] = $product;
        }
        $defaultOpenTab = "Chilled";
        $organisedProducts = [];

        $productList = Product::orderby('category')->orderby('subcategory')->orderby('name')->get();

        foreach ($productList as $product) {
            $organisedProducts[$product->category][$product->subcategory][] = $product;
        }
        return view("soh.assign", [
            "title" => $title,
            "productList" => $productList,
            "assignedMap" => $assignedmap,
            "categories" => $categories,
            "organisedProducts" => $organisedProducts,
            "defaultOpenTab" => $defaultOpenTab
        ]);
    }

    public function saveAssigned(Request $request)
    {
        //no validation required i suppose, maybe check at least 1 product?

        $this->validate(
            $request,
            [
                'sohList' => ['required'],
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

    public function view(Request $request, $response = "")
    {
        $title = "View Stock on Hand Counts";

        $sohs = new StockOnHand;
        $searchFields["stock_on_hands"] = $sohs->getSearchable();

        $search = $request->search;
        $sort = $request->sort;
        $sortDirection = "desc";

        if ($sort == null) {
            $sort = "id";
        }

        $modelSearch = new ModelSearchv4(StockOnHand::class, $searchFields, $searchFields, ["table" => "stock_on_hands", "field" => "store_id", "value" => $this->store->id]);
        $sohs = $modelSearch->search($search, $sort, $sortDirection);

        return view("soh.view", [
            "title" => $title,
            "sohs" => $sohs,
            "searchFields" => $searchFields['stock_on_hands'],
            "search" => $search,
            "sort" => $sort,
            "response" => $response
        ]);
    }
    //delete count
    public function destroy(StockOnHand $soh, Request $request)
    {

        $response = "Sucessfully deleted count #" . $soh->id . " counted on " . $soh->created_at->format('d M Y') . ".";
        $soh->delete();
        return $this->view($request, $response);
    }

    //allow for the printing of a weeks worth of SOH data?

    public function print()
    {
    }

    public function weeklySummary(Request $request)
    {
        $fc = new ForecastingController();

        //if date check fails run dates for this week only
        if (!$fc->checkIsAValidDate($request->date)) {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        } else {
            //valid passed date generate those dates
            $startDate = Carbon::parse($request->date)->startOfWeek();
            $endDate = Carbon::parse($request->date)->endOfWeek();
        }
        $title = "Weekly Stock on Hand";

        $counts = StockOnHand::where("store_id", $this->store->id)
            ->whereBetween('created_at', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderby('created_at', 'asc')
            ->get();

        $countMap = [];
        $productMap = [];
        foreach ($counts as $count) {
            $products = $count->products()->with("units")->get();

            foreach ($products as $product) {

                if (!isset($counter[$product->id][$count->created_at->format('D')])) {
                    $countMap[$product->id][$count->created_at->format('D')] = 0;
                }

                $countMap[$product->id][$count->created_at->format('D')] += ($product->pivot->count / $product->units->quantity);

                $productMap[$product->id] = $product;
            }

            //var_dump($countMap);
        }


        //dd($counts);
        $days = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
        $chartData1 = $this->chartData($countMap, $productMap, $days);

        return view("soh.weekly", [
            "title" => $title,
            "startDate" => $startDate,
            "endDate" => $endDate,
            "counts" => $counts,
            "countMap" => $countMap,
            "productMap" => $productMap,
            "days" => $days,
            "chartData1" => $chartData1
        ]);
    }

    public function chartData($countMap, $productMap, $days)
    {

        $chartData1 = [];

        $headers[] = "Day";
        foreach ($productMap as $pid => $product) {
            $headers[] = $product->name;
        }

        $chartData1[] = $headers;

        foreach ($days as $day) {
            $tempArray = [];
            $tempArray[] = $day;

            foreach ($productMap as $pid => $product) {
                if (isset($countMap[$pid][$day])) {
                    $tempArray[] = $countMap[$pid][$day];
                } else {
                    $tempArray[] = 0;
                }
            }
            //dd($tempArray);
            $chartData1[] = $tempArray;
        }

        return json_encode($chartData1);
    }

    public function dateSelect()
    {
        $title = "Select Week";
        $heading = "Select Week";
        $text = "Pick week to view stock on hand data";
        $action = route("soh.weekly");

        return view('general.date-select', ["title" => $title, "heading" => $heading, "label" => $text, "route" => $action]);
    }
}
