<?php

namespace App\Http\Controllers\Api;

use App\Models\ContentPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentController extends ApiController
{
    private const ALLOWED_TYPES = ['privacy_policy', 'terms', 'help_center'];

    public function show(Request $request): JsonResponse
    {
        $type = $request->query('type');

        if (!in_array($type, self::ALLOWED_TYPES, true)) {
            return $this->error(
                'نوع المحتوى غير صحيح. القيم المسموحة: ' . implode(', ', self::ALLOWED_TYPES),
                422
            );
        }

        $page = ContentPage::find($type);

        if (!$page) {
            return $this->error('المحتوى غير متوفر حالياً.', 404);
        }

        if ($type === 'help_center') {
            return $this->success([
                'type'     => $page->type,
                'title_ar' => $page->title_ar,
                'items'    => $page->items ?? [],
            ]);
        }

        return $this->success([
            'type'       => $page->type,
            'title_ar'   => $page->title_ar,
            'content_ar' => $page->content_ar ?? '',
        ]);
    }
}
