<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Waste;
use App\Models\Forecast;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Http\Controllers\Types\UserAccessController;

class DashboardController extends UserAccessController
{
    public $startDate;
    public $endDate;

    public function __construct()
    {
        //what the bizarre way to call a constructor in php.
        parent::__construct();
        $this->startDate = Carbon::now()->startOfWeek();
        $this->endDate = Carbon::now()->endOfWeek();
    }

    public function index()
    {

        $orders = Order::where("store_id", $this->store->id)
            ->whereBetween("delivery_date", [$this->startDate->format("Y-m-d"), $this->endDate->format("Y-m-d")])
            ->orderby("delivery_date", "asc")
            ->get();

        $forecasts = Forecast::where("store_id", $this->store->id)
            ->whereBetween("date", [$this->startDate->format("Y-m-d"), $this->endDate->format("Y-m-d")])
            ->orderby("date", "asc")
            ->get();

        $forecastTotal = $this->totalforecast($forecasts);

        [$wasteChartData, $totalWastes, $wasteTotal] = $this->wasteBuilder();

        $inventory = Inventory::orderby('created_at', 'desc')
            ->where('store_id', $this->store->id)
            ->get()
            ->first();
        //check for no inventory input at all
        if (is_null($inventory)) {
            $inventoryChart = $inventoryValue = $inventoryCount = $inventory = null;
        } else {
            [$inventoryChart, $inventoryValue, $inventoryCount, $inventory] = $this->inventoryBuilder($inventory);
        }

        //dd($inventoryChart);
        $title = "Dashboard";

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

    private function totalforecast($forecasts)
    {
        $sum = 0;

        foreach ($forecasts as $forecast) {
            $sum += $forecast->value;
        }

        return $sum;
    }

    private function wasteBuilder()
    {
        $wastes = Waste::where("store_id", $this->store->id)
            ->whereBetween("created_at", [$this->startDate->format("Y-m-d"), $this->endDate->format("Y-m-d")])
            ->orderby("created_at", "asc")
            ->get();

        $wc = new WasteController();

        [$totalValue, $wasteDateMap, $catMap] = $wc->wasteCalc($wastes);

        $chartData1 = $wc->chartData1($wasteDateMap);

        return [$chartData1, $wastes->count(), $totalValue];
    }

    private function inventoryBuilder(Inventory $inventory)
    {
        $ic = new InventoryController();



        $productMappings = $inventory
            ->products()
            ->orderby('category', 'asc')
            ->with("units")
            ->get();

        [$catSummary, $totalQuantity, $totalValue] = $ic->fullCalc($productMappings);
        [$chartData1, $chartData2] = $ic->gatherChartData($catSummary);

        return [$chartData2, $totalValue, $totalQuantity, $inventory];
    }
}
