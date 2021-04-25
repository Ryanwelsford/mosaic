<?php

namespace App\Http\Helpers\ModelSearch;

use Illuminate\Support\Facades\DB;

/*****************************************
 *expand the previous search classes to allow for joining of tables, having an value to always restrict by, and search/return fields
 *
 *Class will always return an instance of the passed class, i.e. Store model, without any join fields stay the same and can be accessed normally
 *i.e store->id when joining to prevent issues with duplicate keys all fields are returned as store->store_id, this means that in theory user id
 *could be accessed with store->user_id
 *
 *****************************************/

class ModelSearchv4
{
    private $returnType;
    private $searchableFields;
    private $returnFields;
    private $restriction;
    private $join;
    /*************
     * $fields and join arrays should be
     * [
     *      ["tablename"] => ["id", "name"],
     *      ["tablename"] => ["id", "name"],
     * ]
     *  $restriction currently only allows for a single required where clause (where stores.id = "726" for instance)
     *  restriction format is "table" => "table_name", "field" => "column to restrict by", "value" => "value to check for"
     */

    //maybe create v4 with return type, searchable fields, return fields, table name not required, restriction and join?


    public function __construct($returnType, $searchableFields, $returnFields = [], $restriction = [], $join = [])
    {
        $this->returnType = $returnType;
        $this->searchableFields = $searchableFields;

        //allow for passing of no return fields, instead search fields will be returned
        //tbf without the joins return fields is pointless anyway.
        if (empty($returnFields)) {
            $this->returnFields = $searchableFields;
        } else {
            $this->returnFields = $returnFields;
        }

        $this->restriction = $restriction;
        $this->join = $join;
    }

    //search all table fields, allows for sort only and search only
    public function search($searchValue, $sort = "id", $sortDirection = "desc")
    {
        //guard for malformed searches
        if ($searchValue == null || $searchValue == '' || !isset($searchValue)) {

            return $this->returnDefault($sort, $sortDirection);
        }

        //build query up based on search fields and value
        $results = $this->queryConstruct($searchValue, $sort, $sortDirection);

        //return results
        return $results;
    }

    //build query
    public function queryConstruct($searchValue, $sort, $sortDirection)
    {
        $query = $this->returnType::query();

        $query = $this->selectConstruct($query);
        $query = $this->whereConstruct($query, $searchValue);
        $this->orderbyConstruct($query, $sort, $sortDirection);
        $query = $this->joinQuery($query);

        $results = $this->executeQuery($query);

        return $results;
    }

    //build select statement based on returnable fields
    private function selectConstruct($query)
    {
        //if join does not exist just return query, this section is only required due to potenial for duplicate table keys

        if (empty($this->join)) {
            return $query;
        }

        foreach ($this->returnFields as $tablekey => $table) {
            foreach ($table as $field) {
                $string = $tablekey . "." . $field . " as " . $tablekey . "_" . $field;

                $query = $query->addSelect($string);
            }
        }

        return $query;
    }

    //build where clause
    //change to closure version when restriction is required

    private function restrictConstruct($query)
    {
        if (!empty($this->restriction)) {
            $query->where($this->restriction['table'] . "." . $this->restriction['field'], "=", $this->restriction['value']);
        }
        return $query;
    }
    private function whereConstruct($query, $searchValue)
    {
        //allows for a single restriction currently
        $query = $this->restrictConstruct($query);
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

    //update query string with sort as required
    private function orderbyConstruct(&$query, $sort, $sortDirection)
    {

        $tabletoSortBy = false;

        //results in issues wherein you attempt to have two of the same key name sorted by
        foreach ($this->searchableFields as $tablekey => $table) {
            foreach ($table as $field) {
                if ($sort == $field) {
                    $tabletoSortBy = $tablekey;
                }
            }
        }
        //i.e. sort was not matched as field in searchables
        if (!$tabletoSortBy) {
            //where ever sort does not exist return order by id
            return;
        } else {
            $query->orderby($tabletoSortBy . "." . $sort, $sortDirection);
        }
    }

    //join required tables
    private function joinQuery($query)
    {
        //$joins["users"] = ["table.pk", "foreign.pk"];

        if (!empty($this->join)) {
            foreach ($this->join as $table => $fields) {

                $tablePk = $fields[0];
                $foriegnKey = $fields[1];
                $query = $query->join($table, $tablePk, "=", $foriegnKey);
            }
        }

        return $query;
    }

    //fire query builder return results
    private function executeQuery($query)
    {
        //dd($query->toSql());
        return $query->get();
    }

    //return default does not need where clauses so, but does still need restriction
    private function returnDefault($sort, $sortDirection)
    {
        $query = $this->returnType::query();
        $query = $this->selectConstruct($query);
        $query = $this->restrictConstruct($query);
        $this->orderbyConstruct($query, $sort, $sortDirection);
        $query = $this->joinQuery($query);
        $results = $this->executeQuery($query);

        return $results;
    }
}
