<?php

namespace App\Models;

use App\Models\Unit;
use App\Models\Order;
use App\Models\Receipt;
use App\Models\Inventory;
use App\Models\StockOnHand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

//products are the core component of this build
//has the most relationships by far
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

    //return searchables
    protected $searchable = [
        'id',
        'name',
        'code',
        'category',
        'subcategory'
    ];

    public function getSearchable()
    {
        return $this->searchable;
    }
    //gather assocaited units
    //this needs fixing should only ever return a single row now
    public function units()
    {
        return $this->hasOne(Unit::class);
    }

    //refil product entry for saving
    public function fillItem($id, $name, $code, $category, $subcategory)
    {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->category = $category;
        $this->subcategory = $subcategory;
    }

    //get associated menu of a given product
    public function menus()
    {
        return $this->belongsToMany(Menu::class);
    }

    //get associated orders of a given product with quanitties
    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot("quantity");
    }

    //get associated receipts with quantites
    public function receipts()
    {
        return $this->belongsToMany(Receipt::class)->withPivot("quantity");
    }

    //this may need to have the belongsToMany aspect updated due to the key being called soh_id rather than stock_on_hand_id
    public function stockOnHands()
    {
        //return stock on hands that include this product along with input data
        return $this->belongsToMany(StockOnHand::class)->withPivot("count");
    }

    //get a list of stores that have this product assigned in soh
    public function stores()
    {
        return $this->belongsToMany(Store::class);
    }

    //return associatd wastes that have this product along with quantity
    public function wastes()
    {
        return $this->belongsToMany(Waste::class)->withPivot("quantity");
    }

    //get associated inventories with quantites
    public function inventories()
    {
        return $this->belongsToMany(Inventory::class)->withPivot("quantity");
    }
}
