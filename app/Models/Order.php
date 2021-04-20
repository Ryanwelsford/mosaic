<?php

namespace App\Models;

use App\Models\Product;
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

    public function store()
    {
        return $this->belongsTo(Store::class, "store_id", "id");
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, "menu_id", "id");
    }

    public function products() {
        return $this->belongsToMany(Product::class);
    }
}
