<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\DB;

/*****************************************
 *expand the previous search class to allow for joining of tables
 *****************************************/

class ModelSearchv2
{
    //return type is a string of the given class type using a laravel model
    private $returnType;
    //array of fields to be searched
    private $searchableFields;
    //optional parameter for any default searches required, allows for sorting of outputs without setting a sort field
    private $join;
    private $tablename;

    //maybe create v4 with return type, searchable fields, return fields, table name not required, restriction and join?
    public function __construct($returnType, $searchableFields, $tablename, $join = [])
    {
        $this->returnType = $returnType;
        $this->searchableFields = $searchableFields;
        $this->tablename = $tablename;
        $this->join = $join;
    }

    //search all table fields
    public function search($searchValue, $sort = "id", $sortDirection = "desc")
    {
        //guard for malformed searches
        if ($searchValue == null || $searchValue == '' || !isset($searchValue)) {
            return $this->returnDefault();
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

        //adjusted to add all columns that are searchable as part of return information
        $query = $this->returnType::query();
        $query = $this->selectConstruct($query);


        foreach ($this->searchableFields as $tablekey => $table) {
            foreach ($table as $field) {
                $query->orWhere($tablekey . "." . $field, "LIKE", "%" . $searchValue . "%");
            }
        }

        return $query;
    }

    private function selectConstruct($query) {
        foreach ($this->searchableFields as $tablekey => $table) {

            foreach ($table as $field) {
                $string = $tablekey . "." . $field . " as " . $tablekey . "_" . $field;

                $query = $query->addSelect($string);

            }
        }

        return $query;
    }

    //make use of static functions of models to return a simple defaultly ordered set of results
    private function returnDefault()
    {
        $query = $this->returnType::orderby($this->tablename . "." . "id", "desc");
        $query = $this->selectConstruct($query);
        $results = $this->executeQuery($query);
        return $results;
    }

    //update query string with sort as required
    private function sort(&$query, $sort, $sortDirection)
    {

        //guard against incorrect field passes, default to sort by id
        if ($sort == "id") {
            $query->orderby($this->tablename . "." . "id", $sortDirection);
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

        return $query->get();
    }
}
