<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AnswerQuestionRequest;
use App\Http\Requests\Api\UpdateStageRequest;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\ConversationSummaryResource;
use App\Http\Resources\GuidedAnswerResource;
use App\Http\Resources\GuidedQuestionResource;
use App\Models\Conversation;
use App\Models\GuidedAnswer;
use App\Models\GuidedQuestion;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationController extends ApiController
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    /**
     * List all conversations for authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $userId        = $request->user()->id;
        $conversations = Conversation::with(['user1.profileImages', 'user2.profileImages'])
            ->where('user1_id', $userId)
            ->orWhere('user2_id', $userId)
            ->orderByDesc('updated_at')
            ->paginate(20);

        return $this->success([
            'data'         => ConversationSummaryResource::collection($conversations->items()),
            'total'        => $conversations->total(),
            'per_page'     => $conversations->perPage(),
            'current_page' => $conversations->currentPage(),
            'last_page'    => $conversations->lastPage(),
        ]);
    }

    /**
     * Get full conversation details.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $conversation = Conversation::with([
            'user1.profileImages',
            'user1.privacySettings',
            'user2.profileImages',
            'user2.privacySettings',
            'request',
        ])->find($id);

        if (!$conversation) {
            return $this->error('المحادثة غير موجودة.', 404);
        }

        return $this->success(new ConversationResource($conversation));
    }

    /**
     * Get guided questions and answers for a conversation.
     */
    public function questions(Request $request, string $id): JsonResponse
    {
        $user         = $request->user();
        $conversation = Conversation::find($id);

        if (!$conversation) {
            return $this->error('المحادثة غير موجودة.', 404);
        }

        $questions = GuidedQuestion::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $myAnswers      = GuidedAnswer::where('conversation_id', $id)
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('question_id');

        $partnerId      = $conversation->getPartnerId($user->id);
        $partnerAnswers = GuidedAnswer::where('conversation_id', $id)
            ->where('user_id', $partnerId)
            ->get()
            ->keyBy('question_id');

        $result = $questions->map(function ($question) use ($myAnswers, $partnerAnswers) {
            return [
                'question'       => new GuidedQuestionResource($question),
                'my_answer'      => $myAnswers->has($question->id)
                    ? new GuidedAnswerResource($myAnswers[$question->id])
                    : null,
                'partner_answer' => $partnerAnswers->has($question->id)
                    ? new GuidedAnswerResource($partnerAnswers[$question->id])
                    : null,
            ];
        });

        return $this->success([
            'questions'                 => $result,
            'questions_completed_u1'    => $conversation->questions_completed_u1,
            'questions_completed_u2'    => $conversation->questions_completed_u2,
            'total_questions'           => $questions->count(),
            'is_chat_unlocked'          => $conversation->is_chat_unlocked,
        ]);
    }

    /**
     * Submit an answer to a guided question.
     */
    public function answer(AnswerQuestionRequest $request, string $id, string $qid): JsonResponse
    {
        $user         = $request->user();
        $conversation = Conversation::find($id);

        if (!$conversation) {
            return $this->error('المحادثة غير موجودة.', 404);
        }

        $question = GuidedQuestion::find($qid);
        if (!$question || !$question->is_active) {
            return $this->error('السؤال غير موجود.', 404);
        }

        // Upsert answer
        $isNew = !GuidedAnswer::where('conversation_id', $id)
            ->where('user_id', $user->id)
            ->where('question_id', $qid)
            ->exists();

        GuidedAnswer::updateOrCreate(
            [
                'conversation_id' => $id,
                'user_id'         => $user->id,
                'question_id'     => $qid,
            ],
            [
                'answer_text' => $request->answer_text,
                'answered_at' => now(),
            ]
        );

        // Count completions for this user
        if ($isNew) {
            $totalQuestions = GuidedQuestion::where('is_active', true)->count();
            $answeredCount  = GuidedAnswer::where('conversation_id', $id)
                ->where('user_id', $user->id)
                ->count();

            $completionField = $conversation->user1_id === $user->id
                ? 'questions_completed_u1'
                : 'questions_completed_u2';

            $conversation->update([$completionField => $answeredCount]);
            $conversation->refresh();

            // Check if both users have answered all questions
            $u1Done = $conversation->questions_completed_u1 >= $totalQuestions;
            $u2Done = $conversation->questions_completed_u2 >= $totalQuestions;

            if ($u1Done && $u2Done && !$conversation->is_chat_unlocked) {
                $conversation->update([
                    'is_chat_unlocked' => true,
                    'stage'            => 'ihtimam',
                ]);

                // Notify both users
                $partner = $conversation->user1_id === $user->id
                    ? $conversation->user2
                    : $conversation->user1;

                $this->notificationService->send(
                    $user,
                    'chat_unlocked',
                    'تم فتح المحادثة',
                    'اكتمل التعارف! يمكنكما الآن المحادثة بحرية.',
                    ['conversation_id' => $conversation->id]
                );

                if ($partner) {
                    $this->notificationService->send(
                        $partner,
                        'chat_unlocked',
                        'تم فتح المحادثة',
                        'اكتمل التعارف! يمكنكما الآن المحادثة بحرية.',
                        ['conversation_id' => $conversation->id]
                    );
                }
            }
        }

        return $this->success([], 'تم حفظ الإجابة بنجاح.');
    }

    /**
     * Update conversation stage.
     */
    public function updateStage(UpdateStageRequest $request, string $id): JsonResponse
    {
        $user         = $request->user();
        $conversation = Conversation::find($id);

        if (!$conversation) {
            return $this->error('المحادثة غير موجودة.', 404);
        }

        $stageOrder = ['taaaruf' => 1, 'ihtimam' => 2, 'jiddiyya' => 3, 'family' => 4, 'khitba' => 5];
        $newStage   = $request->stage;
        $current    = $stageOrder[$conversation->stage] ?? 1;
        $requested  = $stageOrder[$newStage] ?? 1;

        // Must progress forward
        if ($requested <= $current) {
            return $this->error('يجب أن تكون المرحلة الجديدة أعلى من المرحلة الحالية.', 422);
        }

        $conversation->update(['stage' => $newStage]);

        // Notify partner
        $partnerId = $conversation->getPartnerId($user->id);
        $partner   = $conversation->user1_id === $user->id ? $conversation->user2 : $conversation->user1;

        if ($partner) {
            $this->notificationService->send(
                $partner,
                'stage_updated',
                'تحديث في العلاقة',
                'تم الانتقال إلى مرحلة جديدة: ' . $newStage,
                ['conversation_id' => $conversation->id, 'stage' => $newStage]
            );
        }

        return $this->success(['stage' => $newStage], 'تم تحديث المرحلة بنجاح.');
    }

    /**
     * Update conversation last activity.
     */
    public function updateActivity(Request $request, string $id): JsonResponse
    {
        $conversation = Conversation::find($id);

        if (!$conversation) {
            return $this->error('المحادثة غير موجودة.', 404);
        }

        $conversation->touch();

        return $this->success([], 'تم تحديث النشاط.');
    }
}
