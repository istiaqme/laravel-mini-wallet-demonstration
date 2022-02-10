<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => "Istiaq Hasan",
            'email' => "istiaq.me@gmail.com",
            'password' => hash("sha512", "123456"),
            'currency' => 'USD',
            'current_balance' => 99999999999999,
            'wallet_id' => "istiaq.me@gmail.com@002022021@USD",
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('users')->insert([
            'name' => "Test User",
            'email' => "test@gmail.com",
            'password' => hash("sha512", "123456"),
            'currency' => 'EUR',
            'current_balance' => 9900,
            'wallet_id' => "test@gmail.com@002032025@EUR",
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
