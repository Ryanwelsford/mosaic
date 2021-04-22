<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Receipt extends Model
{
    use HasFactory;

    public function fillItem($id, $date, $reference, $store_id)
    {
        $this->id = $id;
        $this->date = $date;
        $this->reference = $reference;
        $this->store_id = $store_id;
    }

    public function fillItemArray($array)
    {
        $this->id = $array['id'];
        $this->date = $array['date'];
        $this->reference = $array['reference'];
        $this->store_id = $array['store_id'];
    }

    public function getDate()
    {
        $date = new Carbon($this->date);
        return $date;
    }

    public function store()
    {
        return $this->belongsTo(Store::class, "store_id", "id");
    }
}
