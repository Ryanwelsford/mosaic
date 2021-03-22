<?php

namespace App\Http\Helpers;

use Carbon\Carbon;

/*****************************************************************
 *This class is based around a 13 period year 52/13 = 4
 *Each Period contains 4 weeks
 *Everytime a period increases week should return to 1, week cannot be greater than 4, period than 13
 *Currently only works when passed a date after the base date given
 ******************************************************************/
class PeriodWeekDate
{
    protected $period;
    protected $week;
    protected $day;

    //build and construct class vars based on carbon date instance
    public function __construct(Carbon $date)
    {
        $outputs = $this->periodCalc($date);

        $this->period = $outputs['period'];
        $this->week = $outputs["week"];
        $this->day = $outputs['track'];
    }

    //debug
    public function dump()
    {
        echo "Period: " . $this->period;
        echo "Week: " . $this->week;
        echo "Day: " . $this->day;
    }

    //iterate day perform week check
    public function increaseDay()
    {
        $this->day++;

        if ($this->day > 7) {
            $this->day = 1;
            $this->increaseWeek();
        }
    }

    //iterate week perform period check
    public function increaseWeek()
    {

        $this->week++;
        if ($this->week > 4) {
            $this->week = 1;
            $this->increasePeriod();
        }
    }

    //iterate period
    public function increasePeriod()
    {
        $this->period++;

        if ($this->period > 13) {
            $this->period = 1;
        }
    }

    //get values
    public function getPeriod()
    {
        return $this->period;
    }

    public function getWeek()
    {
        return $this->week;
    }


    public function toString()
    {
        $string = "Period: " . $this->getPeriod() . " Week: " . $this->getWeek();
        return $string;
    }

    //print and iterate used when looping through object in templates
    public function toStringIterate()
    {

        $this->increaseDay();
        return $this->toString();
    }

    //calculate period of given carbon date.
    private function periodCalc(Carbon $dateInput)
    {
        //base date given by client
        $baseDate = Carbon::parse("2021-03-15");
        $period = 4;
        $week = 4;


        $dateDif = $baseDate->diffInDays($dateInput);
        $track = 1;

        //loop through days increase period and and week as required.
        //could probably change this to use weeks%4+1 in some way instead of looping through days.
        while ($dateDif > 1) {
            $track++;

            if ($track > 7) {
                $track = 1;
                $week++;
            }

            if ($week > 4) {
                $week = 1;
                $period++;
            }

            if ($period > 13) {
                $period = 1;
            }

            $dateDif -= 1;
        }

        return [
            "period" => $period,  "week" => $week, "track" => $track
        ];
    }
}
