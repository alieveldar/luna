<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $food = Activity::create(['name' => 'Продовольствие']);
        $vehicles = Activity::create(['name' => 'Автомобили']);
        $services = Activity::create(['name' => 'Услуги']);
        $technology = Activity::create(['name' => 'Технологии']);

        $meatProducts = Activity::create([
            'name' => 'Мясные продукты',
            'parent_id' => $food->id
        ]);
        $dairyProducts = Activity::create([
            'name' => 'Молочные продукты',
            'parent_id' => $food->id
        ]);
        $bakery = Activity::create([
            'name' => 'Выпечка',
            'parent_id' => $food->id
        ]);

        $trucks = Activity::create([
            'name' => 'Грузовики',
            'parent_id' => $vehicles->id
        ]);
        $cars = Activity::create([
            'name' => 'Легковые автомобили',
            'parent_id' => $vehicles->id
        ]);
        $motorcycles = Activity::create([
            'name' => 'Мотоциклы',
            'parent_id' => $vehicles->id
        ]);

        $consulting = Activity::create([
            'name' => 'Консалтинг',
            'parent_id' => $services->id
        ]);
        $cleaning = Activity::create([
            'name' => 'Клининг',
            'parent_id' => $services->id
        ]);

        $software = Activity::create([
            'name' => 'Программное обеспечение',
            'parent_id' => $technology->id
        ]);
        $hardware = Activity::create([
            'name' => 'Аппаратное обеспечение',
            'parent_id' => $technology->id
        ]);

        $spareParts = Activity::create([
            'name' => 'Запасные части',
            'parent_id' => $cars->id
        ]);
        $accessories = Activity::create([
            'name' => 'Аксессуары',
            'parent_id' => $cars->id
        ]);
        $maintenance = Activity::create([
            'name' => 'Обслуживание',
            'parent_id' => $cars->id
        ]);

        $webDevelopment = Activity::create([
            'name' => 'Веб-разработка',
            'parent_id' => $software->id
        ]);
        $mobileApps = Activity::create([
            'name' => 'Мобильные приложения',
            'parent_id' => $software->id
        ]);
        $ai = Activity::create([
            'name' => 'Искусственный интеллект',
            'parent_id' => $software->id
        ]);

        $beef = Activity::create([
            'name' => 'Говядина',
            'parent_id' => $meatProducts->id
        ]);
        $pork = Activity::create([
            'name' => 'Свинина',
            'parent_id' => $meatProducts->id
        ]);
        $chicken = Activity::create([
            'name' => 'Курица',
            'parent_id' => $meatProducts->id
        ]);
    }
}
