<?php

namespace App\Http\Helpers;

class ModelSearch
{

    private $returnType;
    private $searchableFields;
    private $defaultOrder;

    public function __construct($returnType, $searchableFields, $defaultOrder = 'id')
    {
        $this->returnType = $returnType;
        $this->searchableFields = $searchableFields;
        $this->defaultOrder = $defaultOrder;
    }

    public function search($searchValue, $sort = "id")
    {
        //guard for malformed searches
        if ($searchValue == null || $searchValue == '' || !isset($searchValue)) {
            return $this->returnDefault();
        }

        $query = $this->queryConstruct($searchValue);

        $this->sort($query, $sort);

        return $query->get();
    }

    private function queryConstruct($searchValue)
    {
        $query = $this->returnType::query();
        foreach ($this->searchableFields as $field) {
            $query->orWhere($field, "LIKE", "%" . $searchValue . "%");
        }

        return $query;
    }

    //make use of static functions of models
    private function returnDefault()
    {
        $results = $this->returnType::orderby($this->defaultOrder, "desc")->get();
        return $results;
    }

    private function sort(&$query, $sort)
    {

        //guard against incorrect field passes, default to sort by id
        if ($sort == "id" || !in_array($sort, $this->searchableFields)) {
            $query->orderby("id", "desc");
        } else {
            $query->orderby($sort, "desc");
        }

        return $query;
    }
}
