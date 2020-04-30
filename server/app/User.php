<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use GoldSpecDigital\LaravelEloquentUUID\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sub','name', 'email', 'token','last_login_date','last_login_ip',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
         'token'
    ];

    public function userRole()
    {
        return $this->belongsTo('App\UserRole','user_roles_id','id');
    }
   
}
