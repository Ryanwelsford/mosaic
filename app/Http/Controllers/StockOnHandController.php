<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\StockOnHand;
use Illuminate\Http\Request;
use App\Http\Helpers\ModelSearchv3;
use App\Http\Controllers\Types\UserAccessController;

//maybe add a reference to SOH table ?
class StockOnHandController extends UserAccessController
{
    public function home()
    {

        $title = "Stock on Hand Home";

        $menuitems = [
            ["title" => "New Count", "anchor" => route('soh.new'), "img" => "/images/icons/new-256.png"],
            ["title" => "Adjust Products", "anchor" => route('soh.assign'), "img" => "/images/icons/edit-256.png"],
            ["title" => "Edit Count", "anchor" => route('soh.view'), "img" => "/images/icons/edit-256.png"],
            ["title" => "View Counts", "anchor" => route('soh.view'), "img" => "/images/icons/view-256.png"],
            ["title" => "Stock on Hand Reports", "anchor" => "/test", "img" => "/images/icons/report-256.png"]
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
        $soh = new StockOnHand;
        $mappedProducts = [];
        $today = new Carbon();

        //if edit
        if (isset($request->id)) {
            $soh = StockOnHand::find($request->id);
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

        $modelSearch = new ModelSearchv3(StockOnHand::class, $searchFields, ["table" => "stock_on_hands", "field" => "store_id", "value" => $this->store->id]);
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

    public function print() {

    }
}
