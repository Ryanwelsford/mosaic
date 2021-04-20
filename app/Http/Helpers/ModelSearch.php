<?php

namespace App\Http\Helpers;

/*****************************************
 *the purpose of this class is to enable the searching of all passed fields for a given model, meaning that you can in theory search a table based on all of its fields.
 *
 * has various guard checks to ensure both that a non-searchable field is attempted to be sorted by or that a non-valid search parameter isnt entered
 *****************************************/

class ModelSearch
{
    //return type is a string of the given class type using a laravel model
    private $returnType;
    //array of fields to be searched
    private $searchableFields;
    //optional parameter for any default searches required, allows for sorting of outputs without setting a sort field
    private $defaultOrder;

    public function __construct($returnType, $searchableFields, $defaultOrder = 'id')
    {
        $this->returnType = $returnType;
        $this->searchableFields = $searchableFields;
        $this->defaultOrder = $defaultOrder;
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
        return $query->get();
    }

    //when ordering only, using a sort function rather than a search
    public function sortOnly($sort, $sortDirection)
    {
        $query = $this->returnType::query();
        $this->sort($query, $sort, $sortDirection);

        return $query->get();
    }

    //build query string concatenating where clauses for single entry search forms
    private function queryConstruct($searchValue)
    {
        $query = $this->returnType::query();
        foreach ($this->searchableFields as $field) {
            $query->orWhere($field, "LIKE", "%" . $searchValue . "%");
        }

        return $query;
    }

    //make use of static functions of models to return a simple defaultly ordered set of results
    private function returnDefault()
    {
        $results = $this->returnType::orderby($this->defaultOrder, "desc")->get();
        return $results;
    }

    //update query string with sort as required
    private function sort(&$query, $sort, $sortDirection)
    {

        //guard against incorrect field passes, default to sort by id
        if ($sort == "id" || !in_array($sort, $this->searchableFields)) {
            $query->orderby($this->defaultOrder, $sortDirection);
        } else {
            //otherwise sort based on field passed
            $query->orderby($sort, $sortDirection);
        }
    }
}
