<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//smaller instance of countable products available on a store level
class StockOnHand extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'store_id',
    ];

    //get searchable fields
    protected $searchable = [
        'id',
        'reference',
        'created_at'
    ];

    public function getSearchable()
    {
        return $this->searchable;
    }

    //get associated store
    public function store()
    {
        return $this->belongsTo(Store::class, "store_id", "id");
    }

    //get associated counted products with count totals
    public function products()
    {
        return $this->belongsToMany(Product::class, "product_stock_on_hand", "soh_id")->withPivot("count");
    }

    //refill for saving
    public function fillItem($id, $store_id, $reference)
    {
        $this->id = $id;
        $this->store_id = $store_id;
        $this->reference = $reference;
    }
}
