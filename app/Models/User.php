<?php

namespace App\Models;

use App\Models\Store;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

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

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function getCorrectName()
    {
        if ($this->isAdmin()) {
            return $this->name;
        } else {
            return $this->stores()->get()->first()->name;
        }
    }

    public function fillItem($id, $username, $email, $password, $privelleges)
    {
        $this->id = $id;
        $this->email = $email;
        $this->name = $username;
        $this->password = Hash::make($password);
        $this->privelleges = $privelleges;
    }

    public function isAdmin()
    {
        $output = false;
        if ($this->privelleges == "admin") {
            $output = true;
        }

        return $output;
    }
}
