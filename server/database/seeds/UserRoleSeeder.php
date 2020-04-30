<?php

use Illuminate\Database\Seeder;
use App\UserRole;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles =['Administrator','Client'];
        for($i=0 ; $i < count($roles) ; $i++){
            $insert = new UserRole();
            $insert->role_name = $roles[$i];
            $insert->save();
        }

    }
}
