<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;

class UserRole extends Model
{
    //

    public function users()
    {
        return $this->hasMany('App\User','user_roles_id');
    }
}
