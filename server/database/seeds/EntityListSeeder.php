<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EntityListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('entity_lists')->insert([
            [ 'entity_name' => 'Airtrip, Inc.', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'Digitalarsenal.io, Inc.', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'Onepanel, Inc.', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'Awari, Inc.', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'Bench Clearers Incorporated', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'Coliving, Inc.', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'Cruxoid, Inc.', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'CWLabs Production, Inc', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'DE Villiers Chocolate, Inc.', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'Echofin, Inc.', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'Esputnik, Inc.', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'Fabudea, Inc.', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'Flying Start Online, Inc.', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'Movement Vault LLC', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'Green Pages Technology Inc', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'Zapping Tv, Inc.', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'Inventoryapp Inc', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'Ivy and Wilde Inc', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'Justagain.com', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'Linckard LLC', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'Pole Press LLC', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'Shake Technologies, Inc.', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'Stackend Solutions LLC', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'Sworld Inc.', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'Untraworking Inc', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'United Agent Services, LLC', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'W3 Eden, Inc.', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'XCloud Networks Delaware', 'created_at' => now(), 'updated_at' => now()],
            [ 'entity_name' => 'Zeemeo LLC', 'created_at' => now(), 'updated_at' => now()]            
        ]);
    }
}
