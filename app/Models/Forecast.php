<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Forecast extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'created_at',
        'store_id',
        'value',
        'date'
    ];


    public function fillItem($id, $store_id, $date, $value)
    {
        $this->id = $id;
        $this->store_id = $store_id;
        $this->date = $date;
        $this->value = $value;
    }

    public function stores()
    {
        return $this->belongsTo(Store::class, "store_id", "id");
    }

    public function getDate() {
        $date = Carbon::parse($this->date);
        return $date;
    }
}
