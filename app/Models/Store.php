<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Forecast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

//probably the second largest class, with various fields and many relationships
//is a user class therefore can login
class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        "id",
        "store_number",
        "name",
        "address1",
        "address3",
        "address2",
        "postcode",
        "user_id"
    ];

    //get searchable fields
    protected $searchable = [
        "id",
        "number",
        "store_name",
        "address1",
        "address3",
        "address2",
        "postcode",
        "user_id",
        "created_at"
    ];

    public function getSearchable()
    {
        return $this->searchable;
    }

    //refil class for saving
    public function fillItem($id, $name, $number, $address1, $address2, $address3, $postcode, $userid)
    {
        $this->id = $id;
        $this->store_name = $name;
        $this->number = $number;
        $this->address1 = $address1;
        $this->address2 = $address2;
        $this->address3 = $address3;
        $this->postcode = $postcode;
        $this->user_id = $userid;
    }

    //only here due to modelserach box returnign a unique class instance, this breaks the carbon sometimes
    public function getCreated($string = '')
    {
        $carbon = new Carbon($string);
        return $carbon->format('d M Y');
    }

    //get associated user data
    public function users()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    //get assigned soh products
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    //get this stores stochonhand entries
    public function stockOnHand()
    {
        return $this->hasMany(StockOnHand::class);
    }

    //get this stores waste entryeis
    public function wastes()
    {
        return $this->hasMany(Waste::class);
    }

    //get this store inventories
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    //gathe rthis stores indivdual forecasts
    public function forecasts() {
        return $this->hasMany(Forecast::class);
    }
}
