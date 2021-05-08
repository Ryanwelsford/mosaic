<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

//base inventory model
class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'store_id',
        'status'
    ];
    //return searchable fields
    protected $searchable = ['id', 'status', 'created_at'];

    public function getSearchable()
    {
        return $this->searchable;
    }
    //refill class
    public function fillItem($id, $store_id, $status)
    {
        $this->id = $id;
        $this->store_id = $store_id;
        $this->status = $status;
    }

    //get store this inventory belongs to
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    //get all associated products
    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot("quantity");
    }
}
