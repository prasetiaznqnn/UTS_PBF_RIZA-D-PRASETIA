<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * 
     */
    public function run()
    {

        DB::table("users")->insert([
            "name" => "Admin",
            "email" => "admin@mail.com",
            "password" => Hash::make("admin"),
            "role" => "admin"
        ]);
    }
}
