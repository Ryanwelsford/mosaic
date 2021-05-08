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


    //get product that the unit belongs to
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    //redudnant class now based on unit class changes
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

    //used by inputs to pull correct data
    public function packQuantity()
    {

        //if pack exists return its quantity based on overall case quantity
        if ($this->pack != "none") {
            $packQ = $this->quantity / $this->pack_quantity;
            return $packQ;
        } else {
            //otherwise just reutrn case data
            return $this->quantity;
        }
    }

    //refill for saving
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
