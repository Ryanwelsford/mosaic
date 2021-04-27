<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Integer;
use App\Http\Helpers\PeriodWeekDate;
use App\Http\Controllers\Types\UserAccessController;

class ForecastingController extends UserAccessController
{
    public function home()
    {

        $title = "Forecasting Home";

        $newFc = route("forecasting.date");

        $menuitems = [
            ["title" => "New Forecast", "anchor" => $newFc, "img" => "/images/icons/new-256.png"],
            ["title" => "Edit Forecast", "anchor" => "/test", "img" => "/images/icons/edit-256.png"],
            ["title" => "View Forecast", "anchor" => "/test", "img" => "/images/icons/view-256.png"],
            ["title" => "Forecasting Reports", "anchor" => "/test", "img" => "/images/icons/report-256.png"]
        ];

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

        return view("forecast.new", [
            "title" => $title,
            "date" => $date,
            "weekAfterDate" => $weekAfterDate
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

        $title = "Forecast Details";
        $dateDif = $starting_date->diffInDays($ending_date);

        //create an instance of class based on a 4 week period, 13 period year.
        $periodWeekDate = new PeriodWeekDate($starting_date);

        return view("forecast.input", [
            "title" => $title,
            "starting_date" => $starting_date,
            "ending_date" => $ending_date,
            "dateDif" => $dateDif,
            "periodWeekDate" => $periodWeekDate
        ]);
    }
}
