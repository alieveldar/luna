<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use App\Models\PhoneNumber;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $buildings = Building::all();
        $activities = Activity::all();

        $organizations = [
            [
                'name' => 'ООО Рога и Копыта',
                'building_id' => $buildings->random()->id,
                'phone_numbers' => ['2-222-222', '3-333-333', '8-923-666-13-13'],
                'activities' => ['Food', 'Meat Products', 'Beef']
            ],
            [
                'name' => 'Тех Решения',
                'building_id' => $buildings->random()->id,
                'phone_numbers' => ['4-444-444', '5-555-555'],
                'activities' => ['Technology', 'Software', 'Web Development']
            ],
            [
                'name' => 'Автозапчасти Плюс',
                'building_id' => $buildings->random()->id,
                'phone_numbers' => ['6-666-666', '7-777-777'],
                'activities' => ['Vehicles', 'Cars', 'Spare Parts']
            ],
            [
                'name' => 'Молоко и Мёд',
                'building_id' => $buildings->random()->id,
                'phone_numbers' => ['8-888-888'],
                'activities' => ['Food', 'Dairy Products']
            ],
            [
                'name' => 'КлинПро Сервис',
                'building_id' => $buildings->random()->id,
                'phone_numbers' => ['9-999-999', '1-111-111'],
                'activities' => ['Services', 'Cleaning']
            ],
            [
                'name' => 'Мобайл Апп Студия',
                'building_id' => $buildings->random()->id,
                'phone_numbers' => ['2-333-444', '3-444-555'],
                'activities' => ['Technology', 'Software', 'Mobile Apps']
            ],
            [
                'name' => 'Булочные Угощения',
                'building_id' => $buildings->random()->id,
                'phone_numbers' => ['4-555-666'],
                'activities' => ['Food', 'Bakery']
            ],
            [
                'name' => 'ИИ Инновации',
                'building_id' => $buildings->random()->id,
                'phone_numbers' => ['5-666-777', '6-777-888'],
                'activities' => ['Technology', 'Software', 'Artificial Intelligence']
            ],
            [
                'name' => 'Грузовики Транс',
                'building_id' => $buildings->random()->id,
                'phone_numbers' => ['7-888-999'],
                'activities' => ['Vehicles', 'Trucks']
            ],
            [
                'name' => 'Магазин Автоаксессуаров',
                'building_id' => $buildings->random()->id,
                'phone_numbers' => ['8-999-000', '9-000-111'],
                'activities' => ['Vehicles', 'Cars', 'Accessories']
            ],
        ];

        foreach ($organizations as $orgData) {
            $organization = Organization::create([
                'name' => $orgData['name'],
                'building_id' => $orgData['building_id'],
            ]);

            // Create phone numbers
            foreach ($orgData['phone_numbers'] as $phoneNumber) {
                PhoneNumber::create([
                    'organization_id' => $organization->id,
                    'number' => $phoneNumber,
                ]);
            }

            // Attach activities
            foreach ($orgData['activities'] as $activityName) {
                $activity = Activity::where('name', $activityName)->first();
                if ($activity) {
                    $organization->activities()->attach($activity->id);
                }
            }
        }
    }
}
