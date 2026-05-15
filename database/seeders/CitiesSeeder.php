<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitiesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cities')->truncate();

        $cities = [
            ['name_ar' => 'عمّان',    'name_en' => 'Amman'],
            ['name_ar' => 'إربد',     'name_en' => 'Irbid'],
            ['name_ar' => 'الزرقاء', 'name_en' => 'Zarqa'],
            ['name_ar' => 'العقبة',   'name_en' => 'Aqaba'],
            ['name_ar' => 'السلط',    'name_en' => 'Salt'],
            ['name_ar' => 'المفرق',   'name_en' => 'Mafraq'],
            ['name_ar' => 'جرش',      'name_en' => 'Jerash'],
            ['name_ar' => 'مادبا',    'name_en' => 'Madaba'],
            ['name_ar' => 'الكرك',    'name_en' => 'Karak'],
            ['name_ar' => 'معان',     'name_en' => 'Maan'],
            ['name_ar' => 'الطفيلة', 'name_en' => 'Tafilah'],
            ['name_ar' => 'عجلون',    'name_en' => 'Ajloun'],
            ['name_ar' => 'رام الله', 'name_en' => 'Ramallah'],
        ];

        DB::table('cities')->insert($cities);
    }
}
