<?php

namespace Database\Seeders;

use App\Models\Building;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    public function run(): void
    {
        $buildings = [
            [
                'address' => 'Москва, ул. Ленна д. 1, офис 3',
                'latitude' => 55.7558,
                'longitude' => 37.6176,
            ],
            [
                'address' => 'Москва, ул. Тверская д. 15, этаж 2',
                'latitude' => 55.7600,
                'longitude' => 37.6100,
            ],
            [
                'address' => 'Москва, ул. Арбат д. 25, офис 5',
                'latitude' => 55.7494,
                'longitude' => 37.5914,
            ],
            [
                'address' => 'Москва, ул. Новый Арбат д. 10, этаж 3',
                'latitude' => 55.7500,
                'longitude' => 37.5800,
            ],
            [
                'address' => 'Москва, ул. Покровка д. 8, офис 12',
                'latitude' => 55.7600,
                'longitude' => 37.6500,
            ],
        ];

        foreach ($buildings as $building) {
            Building::create($building);
        }
    }
}
