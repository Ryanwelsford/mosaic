<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Store;
use App\Models\Product;
use App\Models\Wastelist;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

//discarded producs entry class
class Waste extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'store_id',
        'reference',
        'wastelist_id'
    ];

    //return searchable fields
    protected $searchable = [
        'id',
        'reference',
        'created_at'
    ];

    public function getSearchable()
    {
        return $this->searchable;
    }

    //associate date due to search class rebuilding field names
    public function getCreated($string = '')
    {
        $carbon = new Carbon($string);
        return $carbon->format('d M Y');
    }

    //get associated store
    public function store()
    {
        return $this->belongsTo(Store::class, "store_id", "id");
    }

    //get associated wastelist
    public function wastelist()
    {
        return $this->belongsTo(Wastelist::class, "wastelist_id", 'id');
    }

    //get assocaited products
    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot("quantity");
    }
    //refill for saving

    public function fillItem($id, $reference, $wastelist, $store)
    {
        $this->id = $id;
        $this->reference = $reference;
        $this->wasteList_id = $wastelist;
        $this->store_id = $store;
    }
}
