<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $insert = new User();
        $insert->name = 'Chuck';
        $insert->email = 'furqan.allshore@gmail.com';
        $insert->save();
    }
}
