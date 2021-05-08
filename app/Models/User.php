<?php

namespace App\Models;

use App\Models\Store;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

//an expansion of the base user class in laravel
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        "privelleges"
    ];

    protected $searchable = [
        'id',
        'name',
        'email',
        "privelleges"
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //get store information
    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    //dependant on class type admin/store gat the associated name
    public function getCorrectName()
    {
        if ($this->isAdmin()) {
            return $this->name;
        } else {
            return $this->stores()->get()->first()->store_name;
        }
    }

    //refil class for saving
    public function fillItem($id, $username, $email, $password, $privelleges)
    {
        $this->id = $id;
        $this->email = $email;
        $this->name = $username;
        $this->password = Hash::make($password);
        $this->privelleges = $privelleges;
    }

    //test if class is an stance of admin type
    public function isAdmin()
    {
        $output = false;
        if ($this->privelleges == "admin") {
            $output = true;
        }

        return $output;
    }

    //inverse ensure is a version of store type
    public function isStore()
    {
        $output = false;
        if ($this->privelleges == "store") {
            $output = true;
        }

        return $output;
    }

    public function getSearchable()
    {
        return $this->searchable;
    }
}
