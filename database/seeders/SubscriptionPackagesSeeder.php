<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubscriptionPackagesSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('subscription_packages')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $packages = [
            [
                'id'              => (string) Str::ulid(),
                'name_ar'         => 'الأساسية',
                'coins_per_month' => 30,
                'price_jd'        => 1.990,
                'is_popular'      => false,
                'features_ar'     => json_encode([
                    '30 عملة شهرياً',
                    'عرض الملفات الشخصية',
                    'إرسال طلبات الزواج',
                ]),
                'sort_order' => 1,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'              => (string) Str::ulid(),
                'name_ar'         => 'الذهبية',
                'coins_per_month' => 100,
                'price_jd'        => 4.990,
                'is_popular'      => true,
                'features_ar'     => json_encode([
                    '100 عملة شهرياً',
                    'أولوية في الاقتراحات',
                    'شارة موثّق',
                    'إرسال رسائل غير محدودة',
                ]),
                'sort_order' => 2,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'              => (string) Str::ulid(),
                'name_ar'         => 'المميزة',
                'coins_per_month' => -1,
                'price_jd'        => 9.990,
                'is_popular'      => false,
                'features_ar'     => json_encode([
                    'عملات غير محدودة',
                    'جميع مميزات الذهبية',
                    'دعم أولوية',
                    'إخفاء كامل للملف',
                ]),
                'sort_order' => 3,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('subscription_packages')->insert($packages);
    }
}
