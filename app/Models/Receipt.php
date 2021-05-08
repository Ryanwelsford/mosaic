<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

//returned order products essentially
class Receipt extends Model
{
    use HasFactory;

    //refil based on passed or array for saving
    public function fillItem($id, $date, $reference, $store_id)
    {
        $this->id = $id;
        $this->date = $date;
        $this->reference = $reference;
        $this->store_id = $store_id;
    }

    public function fillItemArray($array)
    {
        $this->id = $array['id'];
        $this->date = $array['date'];
        $this->reference = $array['reference'];
        $this->store_id = $array['store_id'];
    }

    //return searchable fields of class
    protected $searchable = [
        "id",
        "date",
        "reference",
        "created_at",
        "updated_at"
    ];

    public function getSearchable()
    {
        return $this->searchable;
    }

    //get carbon instance of date receipt
    public function getDate()
    {
        $date = new Carbon($this->date);
        return $date;
    }

    //return associated store
    public function store()
    {
        return $this->belongsTo(Store::class, "store_id", "id");
    }

    //get list of products on receipt with the relative quantites
    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot("quantity");
    }
}
