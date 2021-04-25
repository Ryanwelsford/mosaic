<?php

namespace App\Models;

use App\Models\Unit;
use App\Models\Order;
use App\Models\Receipt;
use App\Models\StockOnHand;
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

    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot("quantity");
    }

    public function receipts()
    {
        return $this->belongsToMany(Receipt::class)->withPivot("quantity");
    }

    //this may need to have the belongsToMany aspect updated due to the key being called soh_id rather than stock_on_hand_id
    public function stockOnHands()
    {
        return $this->belongsToMany(StockOnHand::class)->withPivot("count");
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class);
    }

    public function wastes() {
        return $this->belongsToMany(Waste::class)->withPivot("quantity");
    }
}
