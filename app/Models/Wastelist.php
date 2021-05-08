<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//grouping class for waste entries
//smallest overall class only associated to waste entry
class Wastelist extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'description',
        'status'
    ];

    //get searchable fields
    protected $searchable = [
        'name',
        'description',
        'status'
    ];

    public function getSearchable()
    {
        return $this->searchable;
    }

    //refil for saving
    public function fillItem($id, $name, $description, $status)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->status = $status;
    }

    //will need a wastes relationship at some point in time
    public function Wastes()
    {
        return $this->HasMany(Waste::class, 'wastelist_id', 'id');
    }
}
