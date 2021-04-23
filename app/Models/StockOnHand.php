<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOnHand extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'store_id',
    ];

    protected $searchable = [
        'created_at'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, "store_id", "id");
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, "product_stock_on_hand", "soh_id")->withPivot("count");
    }

    public function fillItem($id, $store_id)
    {
        $this->id = $id;
        $this->store_id = $store_id;
    }

    public function getSearchable()
    {
        return $this->searchable;
    }
}
