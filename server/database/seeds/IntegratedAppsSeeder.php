<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IntegratedAppsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('integrated_apps')->insert([
            'app_name' => 'Agent Admin',
            'created_at' => now()
        ]
        );
    }
}
