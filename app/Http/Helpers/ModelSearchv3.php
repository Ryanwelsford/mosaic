<?php

namespace App\Http\Helpers;

/*****************************************
 *expand the previous search class to allow for joining of tables
 *provide method to restrict query by a particular value at all times
 *****************************************/

class ModelSearchv3
{
    //return type is a string of the given class type using a laravel model
    private $returnType;
    //array of fields to be searched
    private $searchableFields;
    //optional parameter for any default searches required, allows for sorting of outputs without setting a sort field
    private $join;
    private $restriction;
    private $tablename;

    public function __construct($returnType, $searchableFields, $restriction = [], $join = [])
    {
        $this->returnType = $returnType;
        $this->searchableFields = $searchableFields;
        //restriction format table, field, value
        $this->restriction = $restriction;
        $this->join = $join;
        $this->tablename = array_keys($this->searchableFields)[0];
    }

    //search all table fields
    public function search($searchValue, $sort = "id", $sortDirection = "desc")
    {
        //guard for malformed searches
        if ($searchValue == null || $searchValue == '' || !isset($searchValue)) {
            return $this->returnDefault($sort, $sortDirection);
        }

        //build query up based on search fields and value
        $query = $this->queryConstruct($searchValue);

        //add optional sort
        $this->sort($query, $sort, $sortDirection);

        //return results
        return $this->executeQuery($query);
    }

    //when ordering only, using a sort function rather than a search
    public function sortOnly($sort, $sortDirection)
    {
        $query = $this->returnType::query();
        $this->sort($query, $sort, $sortDirection);

        return $this->executeQuery($query);
    }

    //build query string concatenating where clauses for single entry search forms
    private function queryConstruct($searchValue)
    {
        $query = $this->returnType::query();

        if (!empty($this->restriction)) {
            $query->where($this->restriction['table'] . "." . $this->restriction['field'], "=", $this->restriction['value']);
        }
        //old attempt
        /*$counter = 1;
        foreach ($this->searchableFields as $tablekey => $table) {
            foreach ($table as $field) {

                if ($counter == 1) {
                    $query->Where($tablekey . "." . $field, "LIKE", "%" . $searchValue . "%");
                    $counter++;
                } else {
                    $query->orWhere($tablekey . "." . $field, "LIKE", "%" . $searchValue . "%");
                }
            }
        }*/

        //attempt using a closure
        //https://stackoverflow.com/questions/22694866/how-to-add-brackets-around-where-conditions-with-laravel-query-builder
        $query->where(function ($query) use ($searchValue) {
            foreach ($this->searchableFields as $tablekey => $table) {
                foreach ($table as $field) {
                    $query->orWhere($tablekey . "." . $field, "LIKE", "%" . $searchValue . "%");
                }
            }
        });


        return $query;
    }

    //make use of static functions of models to return a simple defaultly ordered set of results
    private function returnDefault($sort, $sortDirection)
    {
        if (empty($this->restriction)) {
            $results = $this->returnType::orderby($this->tablename . "." . "id", $sortDirection)->get();
        } else {
            $results = $this->returnType::where(
                $this->restriction['table'] . "." . $this->restriction['field'],
                "=",
                $this->restriction['value']
            )
                ->orderby($sort, $sortDirection)->get();
        }
        //dd($results);
        return $results;
    }

    //update query string with sort as required
    private function sort(&$query, $sort, $sortDirection)
    {

        //guard against incorrect field passes, default to sort by id
        if ($sort == "id") {
            $key = array_keys($this->searchableFields)[0];
            $query->orderby($key . "." . "id", $sortDirection);
        }

        $tabletoSortBy = false;

        foreach ($this->searchableFields as $tablekey => $table) {
            foreach ($table as $field) {
                if ($sort == $field) {
                    $tabletoSortBy = $tablekey;
                }
            }
        }
        //i.e. sort was not matched as field in searchables
        if (!$tabletoSortBy) {
            $query->orderby($this->tablename . "." . "id", $sortDirection);
        } else {
            $query->orderby($tabletoSortBy . "." . $sort, $sortDirection);
        }
    }

    private function executeQuery($query)
    {
        //["join table", "table.pk", "comparisson / =", "= what"]
        if (!empty($this->join)) {
            foreach ($this->join as $each) {
                $query = $query->join($each[0], $each[1], "=", $each[2]);
            }
        }

        //dd($query->toSql());
        return $query->get();
    }
}
