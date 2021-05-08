<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Integer;
use App\Http\Helpers\PeriodWeekDate;
use App\Http\Controllers\Types\UserAccessController;
use App\Models\Forecast;

##forecasting deals with the monetary value expected in any given store for any given week
class ForecastingController extends UserAccessController
{
    //base menu page for forecasting
    public function home(Request $request)
    {

        $title = "Forecasting Home";

        $newFc = route("forecasting.date");
        //god carbon is useful
        $date = new Carbon();
        $lastMonday = Carbon::now()->startOfWeek();

        //TODO add menu specific wording i.e. create, edit, view etc
        //setup forecasting display pages
        $menuitems = [
            ["title" => "New Forecast", "anchor" => $newFc, "img" => "/images/icons/new-256.png"],
            ["title" => "Edit Forecast", "anchor" => $newFc, "img" => "/images/icons/edit-256.png"],
            ["title" => "Seven Day Forecast", "anchor" => route('forecasting.week', [$date->format('Y-m-d')]), "img" => "/images/icons/pound-256.png"],
            ["title" => "Weekly Forecast", "anchor" => route('forecasting.week', [$lastMonday->format('Y-m-d')]), "img" => "/images/icons/pound-256.png"],
            ["title" => "Four Week Forecast", "anchor" => route('forecasting.monthSelect'), "img" => "/images/icons/pound-256.png"],
        ];

        //when user has linked directly thorugh reports link rearrange to display with reports at the top of page
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


    //generic function created to allow for date selection in reporting tools
    public function dateSelect()
    {
        $title = "Generate Forecast";

        //assume forecasts will want to be created from following monday for a single week.
        $date = new Carbon();
        $date = $date->next("monday");
        //add 6 as it is inclusive of the first day
        //display basic weeks input, idea is to setup from monday to sunday for next week as thats the weeks forecasting that will be created
        $weekAfterDate = Carbon::now()->next("monday")->addDays(6);
        $today = new Carbon();

        return view("forecast.new", [
            "title" => $title,
            "date" => $date,
            "weekAfterDate" => $weekAfterDate,
            "today" => $today
        ]);
    }

    //display forecasting entry pages
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

        //find forecasts and remap to a array with date as the key
        $forecasts = Forecast::where("store_id", $this->store->id)->whereBetween("date", [$starting_date, $ending_date])->get();

        $mapped = [];
        foreach ($forecasts as $forecast) {
            $mapped[$forecast->date] = $forecast;
        }


        $title = "Forecast Details";
        //create max days for view
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

    //save input forecasting data
    public function save(Request $request)
    {
        //validation not required as values set through the inputs
        $ids = $request->forecast['id'];
        $value = $request->forecast['value'];
        $dates = $request->forecast['date'];

        //loop through and save each submitted forecast in turn
        for ($i = 0; $i < count($value); $i++) {
            $forecast = new Forecast;
            $id = null;

            //allows for editing of a forecast even if it is within an entry containing new forecasts
            if (isset($ids[$i])) {
                $id = $ids[$i];
                $forecast = Forecast::find($id);
            }

            //fill and save
            $forecast->fillItem($id, $this->store->id, $dates[$i], $value[$i]);
            $forecast->save();
        }

        return $this->confirm();
    }

    //produce confirmation message when forecast successfully entered
    public function confirm()
    {
        $title = "Forecast Confirmation";
        $heading = "Forecast successfully created";
        $text = "Forecast has been added successfully";
        $anchor = route("forecasting.date");

        return view("general.confirmation", ["title" => $title, "text" => $text, "heading" => $heading, "anchor" => $anchor]);
    }

    //based on input date produce a report for the 6 days following it
    public function forecastWeekByDate($dateInput)
    {
        //no longer required due to route level check
        if (!$this->checkIsAValidDate($dateInput)) {
            //return redirect()->route('forecasting.home');
        }

        $title = "Weekly Forecast Summary";
        //gather dates
        [$date, $ending_date] = $this->returnDateSet($dateInput, 6);

        //pull forecasts and map into chart data
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

    //build data into a chartable format through google charts
    public function buildData($forecasts)
    {
        /*$dataformat = [
            ['Day', 'Forecast'],
            ['2004',  1000],
            ['2005',  1170],
            ['2006',  660],
            ['2007',  1030]
        ];*/

        //initialise data variables
        $data = [];
        $data[] = ['Day', 'Forecast'];
        $total = 0;

        //loop through and remap data based on day
        foreach ($forecasts as $forecast) {
            $carbon = Carbon::parse($forecast->date);
            //days cannot repeat as a max of 7 days are passed
            $data[] = [$carbon->format("l"), $forecast->value];
            //sum up value
            $total += $forecast->value;
        }

        //return encoded chart data and total
        return [json_encode($data), number_format($total, 2)];
    }


    //based on date input return date and date 6 days later (inclusive)
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
        //gather date information based on request, ensure date is a valid request
        if (isset($request->date) && $this->checkIsAValidDate($request->date)) {
            $firstOfMonth = Carbon::parse($request->date)->startOfMonth();
            $endOfMonth = Carbon::parse($request->date)->endOfMonth();
        } else {
            //if fails for any reason return this months dates
            $firstOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
        }

        $title = "Monthly Forecast Data";
        //gather forecasts between dates
        $forecasts = Forecast::where("store_id", $this->store->id)->whereBetween("date", [$firstOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])->orderby("date", 'ASC')->get();

        $forecastTotal = 0;
        $runningTotal = 0;
        $weekly = [];
        //loop through forecast data summing up total per week and total overall
        foreach ($forecasts as $forecast) {
            $forecastTotal += $forecast->value;
            $runningTotal += $forecast->value;
            $carbon = Carbon::parse($forecast->date);

            //remap forecasts at end of week or end of monthly input, provides total weekly values
            if ($carbon->format("l") == "Sunday" || $forecast == $forecasts[count($forecasts) - 1]) {
                $weekly[$carbon->startOfWeek()->format('jS M Y')]["value"] = $runningTotal;
                $weekly[$carbon->startOfWeek()->format('jS M Y')]["date"] = $carbon->startOfWeek()->format('Y-m-d');
                $runningTotal = 0;
            }
        }


        return view('forecast.monthly', ["title" => $title, "forecasts" => $forecasts, "forecast_total" => $forecastTotal, "starting_date" => $firstOfMonth, "ending_date" => $endOfMonth, "weekly" => $weekly]);
    }

    //select the month required for monthyl reporting
    public function monthSelect()
    {
        $title = "Select Month";
        $heading = "Select Month";
        $label = "Pick Month to view";
        $route = route("forecasting.monthly");

        return view("general.date-select", ["title" => $title, "heading" => $heading, "label" => $label, "route" => $route]);
    }
}
