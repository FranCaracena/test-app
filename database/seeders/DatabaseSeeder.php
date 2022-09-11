<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Coach;
use App\Models\Player;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Club::factory(20)->create();
        Player::factory(1000)->create();
        Coach::factory(30)->create();
    }
}
