<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Interest;
use App\Models\GuidedQuestion;

class NeyyahSeeder extends Seeder
{
    public function run(): void
    {
        // ── Interests ─────────────────────────────────
        $interests = [
            ['name_ar' => 'القراءة',          'name_en' => 'Reading',       'icon' => '📚'],
            ['name_ar' => 'الرياضة',           'name_en' => 'Sports',        'icon' => '⚽'],
            ['name_ar' => 'الطبخ',             'name_en' => 'Cooking',       'icon' => '🍳'],
            ['name_ar' => 'السفر',             'name_en' => 'Travel',        'icon' => '✈️'],
            ['name_ar' => 'التكنولوجيا',       'name_en' => 'Technology',    'icon' => '💻'],
            ['name_ar' => 'التصوير',           'name_en' => 'Photography',   'icon' => '📷'],
            ['name_ar' => 'الفنون',            'name_en' => 'Arts',          'icon' => '🎨'],
            ['name_ar' => 'التطوع',            'name_en' => 'Volunteering',  'icon' => '🤝'],
            ['name_ar' => 'الطبيعة',           'name_en' => 'Nature',        'icon' => '🌿'],
            ['name_ar' => 'الأفلام',           'name_en' => 'Movies',        'icon' => '🎬'],
            ['name_ar' => 'البرمجة',           'name_en' => 'Programming',   'icon' => '👨‍💻'],
            ['name_ar' => 'الموسيقى',          'name_en' => 'Music',         'icon' => '🎵'],
            ['name_ar' => 'التطوير الذاتي',    'name_en' => 'Self-development','icon' => '🌱'],
            ['name_ar' => 'القرآن الكريم',     'name_en' => 'Quran',         'icon' => '📖'],
            ['name_ar' => 'اللياقة البدنية',   'name_en' => 'Fitness',       'icon' => '💪'],
            ['name_ar' => 'الأعمال التجارية',  'name_en' => 'Business',      'icon' => '📊'],
        ];

        foreach ($interests as $interest) {
            Interest::firstOrCreate(['name_ar' => $interest['name_ar']], $interest + ['is_active' => true]);
        }

        // ── Guided Questions ──────────────────────────
        $questions = [
            [
                'text_ar'     => 'ما هي رؤيتك للحياة الزوجية المثالية؟',
                'hint_ar'     => 'كيف تتخيل يومك مع شريك حياتك؟',
                'category_ar' => 'الرؤية المشتركة',
                'sort_order'  => 1,
            ],
            [
                'text_ar'     => 'كيف تتعامل مع الخلافات والمشكلات في العلاقة؟',
                'hint_ar'     => 'ما أسلوبك في حل النزاعات؟',
                'category_ar' => 'التواصل',
                'sort_order'  => 2,
            ],
            [
                'text_ar'     => 'ما دور الأسرة في قراراتك الكبيرة؟',
                'hint_ar'     => 'كيف ترى مشاركة الأهل في الحياة الزوجية؟',
                'category_ar' => 'الأسرة',
                'sort_order'  => 3,
            ],
            [
                'text_ar'     => 'كيف تتصور تقسيم المسؤوليات المنزلية والمالية؟',
                'hint_ar'     => 'ما رأيك في الأدوار داخل المنزل؟',
                'category_ar' => 'الحياة اليومية',
                'sort_order'  => 4,
            ],
            [
                'text_ar'     => 'كيف تُعبر عن المحبة والتقدير لشريك حياتك؟',
                'hint_ar'     => 'ما طرقك في إظهار الاهتمام؟',
                'category_ar' => 'المشاعر',
                'sort_order'  => 5,
            ],
        ];

        foreach ($questions as $q) {
            GuidedQuestion::firstOrCreate(
                ['text_ar' => $q['text_ar']],
                $q + ['is_active' => true, 'version' => 1]
            );
        }

        $this->command->info('✅ Neyyah seed data created successfully.');
    }
}
