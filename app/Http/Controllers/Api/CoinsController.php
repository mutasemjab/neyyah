<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\EarnCoinsRequest;
use App\Http\Resources\CoinTransactionResource;
use App\Http\Resources\CoinsWalletResource;
use App\Models\CoinTransaction;
use App\Services\CoinService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CoinsController extends ApiController
{
    public function __construct(private CoinService $coinService)
    {
    }

    /**
     * Get current user's wallet.
     */
    public function wallet(Request $request): JsonResponse
    {
        $user   = $request->user();
        $wallet = $user->wallet;

        if (!$wallet) {
            return $this->error('المحفظة غير موجودة.', 404);
        }

        return $this->success(new CoinsWalletResource($wallet));
    }

    /**
     * Claim daily free coins.
     */
    public function claimDaily(Request $request): JsonResponse
    {
        $user    = $request->user();
        $claimed = $this->coinService->claimDailyFree($user);

        if (!$claimed) {
            $wallet       = $user->wallet;
            $resetAt      = $wallet?->daily_free_reset_at;

            return $this->error(
                'لقد استلمت عملاتك المجانية اليوم. يمكنك المطالبة بها مرة أخرى غداً.',
                422,
                ['reset_at' => $resetAt?->toIso8601String()]
            );
        }

        $user->refresh();

        return $this->success(
            new CoinsWalletResource($user->wallet),
            'تم استلام العملات المجانية بنجاح!'
        );
    }

    /**
     * Earn coins through activity.
     */
    public function earn(EarnCoinsRequest $request): JsonResponse
    {
        $user  = $request->user();
        $type  = $request->type;

        $amounts      = ['ad' => 1, 'share' => 2, 'invite' => 5];
        $descriptions = [
            'ad'     => 'مشاهدة إعلان',
            'share'  => 'مشاركة التطبيق',
            'invite' => 'دعوة صديق',
        ];

        $amount      = $amounts[$type];
        $description = $descriptions[$type];

        $this->coinService->earn($user, $amount, $type, $description);

        $user->refresh();

        return $this->success(
            new CoinsWalletResource($user->wallet),
            "تم إضافة {$amount} عملة إلى رصيدك."
        );
    }

    /**
     * Get coin transactions history.
     */
    public function transactions(Request $request): JsonResponse
    {
        $user         = $request->user();
        $transactions = CoinTransaction::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return $this->success([
            'data'         => CoinTransactionResource::collection($transactions->items()),
            'total'        => $transactions->total(),
            'per_page'     => $transactions->perPage(),
            'current_page' => $transactions->currentPage(),
            'last_page'    => $transactions->lastPage(),
        ]);
    }
}
