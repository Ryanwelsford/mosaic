<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//menu model class, allows for grouping of individal products
class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'description',
        'status'
    ];

    //return searchable fields of menu
    protected $searchable = [
        'id',
        'name',
        'description',
        'status',
        'created_at'
    ];

    public function searchable()
    {
        return $this->searchable;
    }

    //refil menu based on input
    public function fillItem($id, $name, $description, $status)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->status = $status;
    }

    //fill menu with an array, marks different usage of view forms than final version
    public function fillArrayItem($menu)
    {
        //again due to view form infromation its one or the other here
        try {
            $this->id = $menu['id'];
            $this->name = $menu['name'];
            $this->description = $menu['description'];
            $this->status = $menu['status'];
        } catch (Exception $e) {
            $this->id = $menu['menu']['id'];
            $this->name = $menu['menu']['name'];
            $this->description = $menu['menu']['description'];
            $this->status = $menu['menu']['status'];
        }
    }

    //get products associated to this class
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
