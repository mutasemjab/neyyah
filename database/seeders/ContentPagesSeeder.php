<?php

namespace Database\Seeders;

use App\Models\ContentPage;
use Illuminate\Database\Seeder;

class ContentPagesSeeder extends Seeder
{
    public function run(): void
    {
        ContentPage::upsert([
            [
                'type'       => 'privacy_policy',
                'title_ar'   => 'سياسة الخصوصية',
                'content_ar' => 'نص سياسة الخصوصية الكامل هنا. يُرجى تحديث هذا النص من لوحة التحكم.',
                'items'      => null,
            ],
            [
                'type'       => 'terms',
                'title_ar'   => 'شروط الاستخدام',
                'content_ar' => 'نص شروط الاستخدام الكامل هنا. يُرجى تحديث هذا النص من لوحة التحكم.',
                'items'      => null,
            ],
            [
                'type'       => 'help_center',
                'title_ar'   => 'مركز المساعدة',
                'content_ar' => null,
                'items'      => json_encode([
                    [
                        'question_ar' => 'كيف أحذف حسابي؟',
                        'answer_ar'   => 'يمكنك حذف حسابك من إعدادات التطبيق ← الحساب ← حذف الحساب.',
                    ],
                    [
                        'question_ar' => 'كيف أتواصل مع الدعم؟',
                        'answer_ar'   => 'يمكنك التواصل معنا عبر قسم "الدعم والإبلاغ" داخل التطبيق، وسنرد عليك خلال 24-48 ساعة.',
                    ],
                    [
                        'question_ar' => 'كيف أوثّق هويتي؟',
                        'answer_ar'   => 'اذهب إلى ملفك الشخصي ← توثيق الهوية، ثم ارفع صورة بطاقتك أو جواز سفرك.',
                    ],
                    [
                        'question_ar' => 'كيف تعمل العملات؟',
                        'answer_ar'   => 'تُستخدم العملات لعرض الملفات الشخصية. تحصل على 5 عملات مجانية يومياً، ويمكنك الحصول على المزيد عبر الاشتراك.',
                    ],
                ]),
            ],
        ], ['type'], ['title_ar', 'content_ar', 'items']);
    }
}
