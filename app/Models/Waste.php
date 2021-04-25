<?php

namespace App\Models;

use App\Models\Store;
use App\Models\Product;
use App\Models\Wastelist;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Waste extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'store_id',
        'reference',
        'wastelist_id'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, "store_id", "id");
    }

    public function wastelist()
    {
        return $this->belongsTo(Wastelist::class, "wastelist_id", 'id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot("quantity");
    }

    public function fillItem($id, $reference, $wastelist, $store)
    {
        $this->id = $id;
        $this->reference = $reference;
        $this->wasteList_id = $wastelist;
        $this->store_id = $store;
    }
}
