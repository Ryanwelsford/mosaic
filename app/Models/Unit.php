<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'price',
        'product_id',
        'pack',
        'quantity',
        "pack_quantity",
        "pack_description"
    ];


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function splitUnit()
    {
        $case = [
            "description" => $this->description,
            "price" => $this->price,
            "quantity" => $this->quantity,
        ];
        $pack = [
            "quantity" => $this->pack_quantity,
            "description" => $this->pack_description,
            "details" => $this->pack
        ];

        return [$case, $pack];
    }

    public function fillItem($case, $pack, $id)
    {
        $this->product_id = $id;
        $this->description = $case['description'];
        $this->quantity = $case['quantity'];
        $this->price = $case['price'];

        $this->pack = $pack['details'];
        $this->pack_quantity = $pack['quantity'];
        $this->pack_description = $pack['description'];
    }
}
