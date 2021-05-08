<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Waste;
use App\Models\Forecast;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Http\Controllers\Types\UserAccessController;

//controller serves to house base store level dashboard pulling in a variety of functions from other controllers
class DashboardController extends UserAccessController
{
    //allow for singluar access to fields, as they are used in all functions
    public $startDate;
    public $endDate;

    //construct initialise start and end dates for dashboard display.
    //maybe add the ability to change the dashboard dates in the future?
    public function __construct()
    {
        //what the bizarre way to call a constructor in php.
        parent::__construct();
        $this->startDate = Carbon::now()->startOfWeek();
        $this->endDate = Carbon::now()->endOfWeek();
    }

    //display dashboard, produce forcast,waste, inventory and delivery information
    public function index()
    {

        //pull orders between start and end date
        $orders = Order::where("store_id", $this->store->id)
            ->whereBetween("delivery_date", [$this->startDate->format("Y-m-d"), $this->endDate->format("Y-m-d")])
            ->orderby("delivery_date", "asc")
            ->get();

        //pull forecasts between start and end date
        $forecasts = Forecast::where("store_id", $this->store->id)
            ->whereBetween("date", [$this->startDate->format("Y-m-d"), $this->endDate->format("Y-m-d")])
            ->orderby("date", "asc")
            ->get();

        //sum forecast
        $forecastTotal = $this->totalforecast($forecasts);

        //chart waste entry data
        [$wasteChartData, $totalWastes, $wasteTotal] = $this->wasteBuilder();

        //input all most recent inventory information
        $inventory = Inventory::orderby('created_at', 'desc')
            ->where('store_id', $this->store->id)
            ->get()
            ->first();
        //check for no inventory input at all
        if (is_null($inventory)) {
            //guards against issues when no inventory information has been created, i.e. a new user
            $inventoryChart = $inventoryValue = $inventoryCount = $inventory = null;
        } else {
            [$inventoryChart, $inventoryValue, $inventoryCount, $inventory] = $this->inventoryBuilder($inventory);
        }


        $title = "Dashboard";

        //return all relevent variables including those used to check on data charts when charts have 0 data

        return view("dashboard.index", [
            "title" => $title,
            "orders" => $orders,
            "forecasts" => $forecasts,
            "forecastTotal" => $forecastTotal,
            "startDate" => $this->startDate,
            "wasteChartData" => $wasteChartData,
            "totalWastes" => $totalWastes,
            "wasteTotal" => $wasteTotal,
            "inventoryValue" => $inventoryValue,
            "inventoryCount" => $inventoryCount,
            "inventoryChart" => $inventoryChart,
            "inventory" => $inventory
        ]);
    }

    //sum up forecastes used within display
    private function totalforecast($forecasts)
    {
        $sum = 0;

        foreach ($forecasts as $forecast) {
            $sum += $forecast->value;
        }

        return $sum;
    }

    //pull waste information from waste controller use to chart new waste map and return
    private function wasteBuilder()
    {
        $wastes = Waste::where("store_id", $this->store->id)
            ->whereBetween("created_at", [$this->startDate->format("Y-m-d"), $this->endDate->format("Y-m-d")])
            ->orderby("created_at", "asc")
            ->get();

        //pull waste controller
        $wc = new WasteController();

        //return waste data from waste controler
        [$totalValue, $wasteDateMap, $catMap] = $wc->wasteCalc($wastes);

        $chartData1 = $wc->chartData1($wasteDateMap);

        return [$chartData1, $wastes->count(), $totalValue];
    }

    //produce and chart the inventory chart from within the inventory controller, return to index for display in one screen
    private function inventoryBuilder(Inventory $inventory)
    {
        //pull inventory controller
        $ic = new InventoryController();

        $productMappings = $inventory
            ->products()
            ->orderby('category', 'asc')
            ->with("units")
            ->get();

        //pull data from inventory controller
        [$catSummary, $totalQuantity, $totalValue] = $ic->fullCalc($productMappings);
        [$chartData1, $chartData2] = $ic->gatherChartData($catSummary);

        return [$chartData2, $totalValue, $totalQuantity, $inventory];
    }
}
