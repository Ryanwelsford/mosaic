<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Integer;
use App\Http\Helpers\PeriodWeekDate;
use App\Http\Controllers\Types\UserAccessController;
use App\Models\Forecast;

class ForecastingController extends UserAccessController
{
    public function home(Request $request)
    {

        $title = "Forecasting Home";

        $newFc = route("forecasting.date");
        //god carbon is useful
        $date = new Carbon();
        $lastMonday = Carbon::now()->startOfWeek();
        //dd($lastMonday->format('Y-m-d'));
        $menuitems = [
            ["title" => "New Forecast", "anchor" => $newFc, "img" => "/images/icons/new-256.png"],
            ["title" => "Edit Forecast", "anchor" => $newFc, "img" => "/images/icons/edit-256.png"],
            ["title" => "Seven Day Forecast", "anchor" => route('forecasting.week', [$date->format('Y-m-d')]), "img" => "/images/icons/pound-256.png"],
            ["title" => "Weekly Forecast", "anchor" => route('forecasting.week', [$lastMonday->format('Y-m-d')]), "img" => "/images/icons/pound-256.png"],
            ["title" => "Four Week Forecast", "anchor" => route('forecasting.monthSelect'), "img" => "/images/icons/pound-256.png"],
        ];

        if (isset($request->report)) {
            $menuitems = [
                ["title" => "Seven Day Forecast", "anchor" => route('forecasting.week', [$date->format('Y-m-d')]), "img" => "/images/icons/pound-256.png"],
                ["title" => "Weekly Forecast", "anchor" => route('forecasting.week', [$lastMonday->format('Y-m-d')]), "img" => "/images/icons/pound-256.png"],
                ["title" => "Four Week Forecast", "anchor" => route('forecasting.monthSelect'), "img" => "/images/icons/pound-256.png"],
                ["title" => "New Forecast", "anchor" => $newFc, "img" => "/images/icons/new-256.png"],
                ["title" => "Edit Forecast", "anchor" => $newFc, "img" => "/images/icons/edit-256.png"],
            ];
        }

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
        ]);
    }

    public function dateSelect()
    {
        $title = "Generate Forecast";

        //assume forecasts will want to be created from following monday for a single week.
        $date = new Carbon();
        $date = $date->next("monday");
        //add 6 as it is inclusive of the first day
        $weekAfterDate = Carbon::now()->next("monday")->addDays(6);
        $today = new Carbon();

        return view("forecast.new", [
            "title" => $title,
            "date" => $date,
            "weekAfterDate" => $weekAfterDate,
            "today" => $today
        ]);
    }

    //TODO add ability to check dates to ensure a min has been achieved.
    public function store(Request $request)
    {
        //attempt to build date strings from request
        try {
            $starting_date = Carbon::parse($request->starting_date);
            $ending_date = Carbon::parse($request->ending_date);
        }
        //if dates are incorrect or badly formatted return to dateSelect
        catch (Exception $e) {
            return $this->dateSelect();
        }

        $forecasts = Forecast::where("store_id", $this->store->id)->whereBetween("date", [$starting_date, $ending_date])->get();

        $mapped = [];
        foreach ($forecasts as $forecast) {
            $mapped[$forecast->date] = $forecast;
        }


        $title = "Forecast Details";
        $dateDif = $starting_date->diffInDays($ending_date);

        //create an instance of class based on a 4 week period, 13 period year.
        $periodWeekDate = new PeriodWeekDate($starting_date);
        //dd($starting_date->format('Y-m-d'));
        return view("forecast.input", [
            "title" => $title,
            "starting_date" => $starting_date,
            "ending_date" => $ending_date,
            "dateDif" => $dateDif,
            "periodWeekDate" => $periodWeekDate,
            "mapped" => $mapped
        ]);
    }

    public function save(Request $request)
    {
        $ids = $request->forecast['id'];
        $value = $request->forecast['value'];
        $dates = $request->forecast['date'];

        for ($i = 0; $i < count($value); $i++) {
            $forecast = new Forecast;
            $id = null;
            if (isset($ids[$i])) {
                $id = $ids[$i];
                $forecast = Forecast::find($id);
            }

            $forecast->fillItem($id, $this->store->id, $dates[$i], $value[$i]);
            $forecast->save();
        }

        return $this->confirm();
    }

    public function confirm()
    {
        $title = "Forecast Confirmation";
        $heading = "Forecast successfully created";
        $text = "Forecast has been added successfully";
        $anchor = route("forecasting.date");

        return view("general.confirmation", ["title" => $title, "text" => $text, "heading" => $heading, "anchor" => $anchor]);
    }

    public function forecastWeekByDate($dateInput)
    {

        if (!$this->checkIsAValidDate($dateInput)) {
            //return redirect()->route('forecasting.home');
        }

        $title = "Weekly Forecast Summary";
        [$date, $ending_date] = $this->returnDateSet($dateInput, 6);
        $forecasts = Forecast::where("store_id", $this->store->id)->whereBetween("date", [$date->format('Y-m-d'), $ending_date->format('Y-m-d')])->orderby("date", 'ASC')->get();
        [$chartData1, $forecastTotal] = $this->buildData($forecasts);

        return view("forecast.week", [
            "title" => $title,
            "chartData1" => $chartData1,
            "starting_date" => $date,
            "ending_date" => $ending_date,
            "forecast_total" => $forecastTotal,
            "forecasts" => $forecasts
        ]);
    }

    //check for valid date format
    //https://stackoverflow.com/questions/19271381/correctly-determine-if-date-string-is-a-valid-date-in-that-format
    function checkIsAValidDate($myDateString)
    {
        return (bool)strtotime($myDateString);
    }

    public function buildData($forecasts)
    {
        /*$dataformat = [
            ['Day', 'Forecast'],
            ['2004',  1000],
            ['2005',  1170],
            ['2006',  660],
            ['2007',  1030]
        ];*/

        $data = [];
        $data[] = ['Day', 'Forecast'];
        $total = 0;

        foreach ($forecasts as $forecast) {
            $carbon = Carbon::parse($forecast->date);
            $data[] = [$carbon->format("l"), $forecast->value];
            $total += $forecast->value;
        }

        //dd($data);
        return [json_encode($data), number_format($total, 2)];
    }


    public function returnDateSet($dateInput, $days = 6)
    {

        $date = Carbon::parse($dateInput);

        $ending_date = Carbon::parse($dateInput);
        $ending_date = $ending_date->addDays($days);

        return [$date, $ending_date];
    }

    //return forecast info for the month.
    public function monthly(Request $request)
    {
        if (isset($request->date) && $this->checkIsAValidDate($request->date)) {
            $firstOfMonth = Carbon::parse($request->date)->startOfMonth();
            $endOfMonth = Carbon::parse($request->date)->endOfMonth();
        } else {
            $firstOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
        }

        $title = "Monthly Forecast Data";
        $forecasts = Forecast::where("store_id", $this->store->id)->whereBetween("date", [$firstOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])->orderby("date", 'ASC')->get();

        $forecastTotal = 0;
        $runningTotal = 0;
        $weekly = [];
        foreach ($forecasts as $forecast) {
            $forecastTotal += $forecast->value;
            $runningTotal += $forecast->value;
            $carbon = Carbon::parse($forecast->date);

            if ($carbon->format("l") == "Sunday" || $forecast == $forecasts[count($forecasts) - 1]) {
                $weekly[$carbon->startOfWeek()->format('jS M Y')]["value"] = $runningTotal;
                $weekly[$carbon->startOfWeek()->format('jS M Y')]["date"] = $carbon->startOfWeek()->format('Y-m-d');
                $runningTotal = 0;
            }
        }


        return view('forecast.monthly', ["title" => $title, "forecasts" => $forecasts, "forecast_total" => $forecastTotal, "starting_date" => $firstOfMonth, "ending_date" => $endOfMonth, "weekly" => $weekly]);
    }

    public function monthSelect()
    {
        $title = "Select Month";
        $heading = "Select Month";
        $label = "Pick Month to view";
        $route = route("forecasting.monthly");

        return view("general.date-select", ["title" => $title, "heading" => $heading, "label" => $label, "route" => $route]);
    }
}
