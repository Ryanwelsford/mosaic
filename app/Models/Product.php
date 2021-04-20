<?php

namespace App\Models;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'code',
        'category',
        'subcategory'
    ];

    protected $searchable = [
        'id',
        'name',
        'code',
        'category',
        'subcategory'
    ];

    public function units()
    {
        return $this->hasOne(Unit::class);
    }

    public function fillItem($id, $name, $code, $category, $subcategory)
    {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->category = $category;
        $this->subcategory = $subcategory;
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class);
    }

    public function getSearchable()
    {
        return $this->searchable;
    }
}
