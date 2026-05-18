<?php

namespace App\Http\Controllers\Api;

use App\Models\SupportTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SupportController extends ApiController
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'type'    => ['required', 'string', 'in:bug,content,harassment,fake,other'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'type.in'          => 'نوع البلاغ غير صحيح.',
            'message.required' => 'نص البلاغ مطلوب.',
            'message.min'      => 'يجب أن يحتوي البلاغ على 10 أحرف على الأقل.',
            'message.max'      => 'نص البلاغ طويل جداً (الحد الأقصى 2000 حرف).',
        ]);

        $user   = $request->user();
        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'type'    => $request->type,
            'message' => $request->message,
        ]);

        $this->notifyAdmin($user, $ticket);

        return $this->success([], 'تم إرسال بلاغك بنجاح.', 201);
    }

    private function notifyAdmin($user, SupportTicket $ticket): void
    {
        $adminEmail = config('mail.admin_email');

        if (!$adminEmail) {
            return;
        }

        try {
            Mail::raw(
                implode("\n", [
                    "بلاغ جديد من تطبيق Neyyah",
                    "----------------------------",
                    "المستخدم : {$user->display_name} ({$user->phone})",
                    "النوع    : {$ticket->type}",
                    "الرسالة  : {$ticket->message}",
                    "التاريخ  : {$ticket->created_at}",
                ]),
                fn ($mail) => $mail
                    ->to($adminEmail)
                    ->subject("بلاغ جديد [{$ticket->type}] - Neyyah")
            );
        } catch (\Throwable $e) {
            Log::error('Support ticket email failed: ' . $e->getMessage());
        }
    }
}
