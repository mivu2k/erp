<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stone;

class StoneSeeder extends Seeder
{
    public function run(): void
    {
        $stones = [
            ['type' => 'Marble', 'name' => 'Calacatta Gold', 'color_finish' => 'Polished'],
            ['type' => 'Marble', 'name' => 'Carrara White', 'color_finish' => 'Honed'],
            ['type' => 'Granite', 'name' => 'Black Galaxy', 'color_finish' => 'Polished'],
            ['type' => 'Granite', 'name' => 'Blue Pearl', 'color_finish' => 'Polished'],
            ['type' => 'Quartzite', 'name' => 'Taj Mahal', 'color_finish' => 'Leathered'],
        ];

        foreach ($stones as $stone) {
            Stone::create($stone);
        }
    }
}
