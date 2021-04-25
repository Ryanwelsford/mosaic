<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wastelist extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'description',
    ];

    protected $searchable = [
        'name'
    ];

    public function getSearchable()
    {
        return $this->searchable;
    }

    public function fillItem($id, $name, $description)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }

    //will need a wastes relationship at some point in time
    public function Wastes() {
        return $this->HasMany(Waste::class, 'wastelist_id', 'id');
    }
}
