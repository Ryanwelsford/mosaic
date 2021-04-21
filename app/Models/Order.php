<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        "id",
        "menu_id",
        "store_id",
        "delivery_date",
        "status",
        "reference"
    ];

    protected $searchable = [
        "id",
        "menu_id",
        "delivery_date",
        "status",
        "reference",
        "created_at"
    ];

    public function getSearchable()
    {
        return $this->searchable;
    }

    public function fillItem($id, $delivery_date, $status, $reference, $menu_id, $store_id)
    {
        $this->id = $id;
        $this->delivery_date = $delivery_date;
        $this->status = $status;
        $this->reference = $reference;
        $this->menu_id = $menu_id;
        $this->store_id = $store_id;
    }

    public function fillItemArray($array)
    {
        $this->id = $array['id'];
        $this->delivery_date = $array['delivery_date'];
        $this->status = $array['status'];
        $this->reference = $array['reference'];
        $this->menu_id = $array['menu_id'];
        $this->store_id = $array['store_id'];
    }

    public function getDeliveryDate()
    {
        $date = new Carbon($this->delivery_date);
        return $date;
    }

    public function store()
    {
        return $this->belongsTo(Store::class, "store_id", "id");
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, "menu_id", "id");
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot("quantity");
    }
}
