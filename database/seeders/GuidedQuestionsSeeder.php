<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GuidedQuestionsSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('guided_questions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $questions = [
            [
                'id'          => (string) Str::ulid(),
                'text_ar'     => 'ما هي رؤيتك للحياة الزوجية المثالية؟',
                'hint_ar'     => 'كيف تتخيل يومك مع شريك حياتك؟',
                'category_ar' => 'الرؤية المشتركة',
                'sort_order'  => 1,
                'is_active'   => true,
            ],
            [
                'id'          => (string) Str::ulid(),
                'text_ar'     => 'كيف تتعامل مع الخلافات والمشكلات في العلاقة؟',
                'hint_ar'     => 'ما أسلوبك في حل النزاعات؟',
                'category_ar' => 'التواصل',
                'sort_order'  => 2,
                'is_active'   => true,
            ],
            [
                'id'          => (string) Str::ulid(),
                'text_ar'     => 'ما دور الأسرة في قراراتك الكبيرة؟',
                'hint_ar'     => 'كيف ترى مشاركة الأهل في الحياة الزوجية؟',
                'category_ar' => 'الأسرة',
                'sort_order'  => 3,
                'is_active'   => true,
            ],
            [
                'id'          => (string) Str::ulid(),
                'text_ar'     => 'كيف تتصور تقسيم المسؤوليات المنزلية والمالية؟',
                'hint_ar'     => 'ما رأيك في الأدوار داخل المنزل؟',
                'category_ar' => 'الحياة اليومية',
                'sort_order'  => 4,
                'is_active'   => true,
            ],
            [
                'id'          => (string) Str::ulid(),
                'text_ar'     => 'كيف تُعبر عن المحبة والتقدير لشريك حياتك؟',
                'hint_ar'     => 'ما طرقك في إظهار الاهتمام؟',
                'category_ar' => 'المشاعر',
                'sort_order'  => 5,
                'is_active'   => true,
            ],
        ];

        DB::table('guided_questions')->insert($questions);
    }
}
