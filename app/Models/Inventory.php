<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'store_id',
        'status'
    ];
    protected $searchable = ['id', 'status', 'created_at'];

    public function getSearchable() {
        return $this->searchable;
    }
    public function fillItem($id, $store_id, $status) {
        $this->id = $id;
        $this->store_id = $store_id;
        $this->status = $status;
    }

    public function store() {
        return $this->belongsTo(Store::class);
    }

    public function products() {
        return $this->belongsToMany(Product::class)->withPivot("quantity");
    }
}
