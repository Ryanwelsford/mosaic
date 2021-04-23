<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        "id",
        "number",
        "name",
        "address1",
        "address3",
        "address2",
        "postcode",
        "user_id"
    ];

    protected $searchable = [
        "id",
        "number",
        "name",
        "address1",
        "address3",
        "address2",
        "postcode",
        "user_id"
    ];

    public function getSearchable()
    {
        return $this->searchable;
    }

    public function fillItem($id, $name, $number, $address1, $address2, $address3, $postcode, $userid)
    {
        $this->id = $id;
        $this->name = $name;
        $this->number = $number;
        $this->address1 = $address1;
        $this->address2 = $address2;
        $this->address3 = $address3;
        $this->postcode = $postcode;
        $this->user_id = $userid;
    }

    public function users()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function stockOnHand()
    {
        return $this->hasMany(Store::class);
    }
}
