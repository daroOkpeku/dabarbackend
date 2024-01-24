<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\StoreTrait;
use Nicolaslopezj\Searchable\SearchableTrait;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, StoreTrait, SearchableTrait;


    protected $searchable  = [
        "columns"=>[
           "users.firstname"=>10,
            "users.lastname"=>10,
            "users.email"=>10,
            "users.role"=>10,
        ]
      ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'verification_code',
        'status',
        'role',
        'password',
    ];



    public function userprofiledetails(){
        // user_id is the foreign key and id is referencing id in user table
        return $this->hasOne(userprofile::class, 'user_id', 'id');
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
