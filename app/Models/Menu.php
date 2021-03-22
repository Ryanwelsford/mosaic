<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'description',
        'status'
    ];

    public function fillItem($id, $name, $description, $status)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->status = $status;
    }

    public function fillArrayItem($menu)
    {
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

    public function products() {
        return $this->belongsToMany(Product::class);
    }
}
