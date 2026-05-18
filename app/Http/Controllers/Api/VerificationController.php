<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\SubmitVerificationRequest;
use App\Models\IdentityVerification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VerificationController extends ApiController
{
    /**
     * Get current verification status for authenticated user.
     */
    public function status(Request $request): JsonResponse
    {
        $user         = $request->user();
        $verification = IdentityVerification::where('user_id', $user->id)->first();

        if (!$verification) {
            return $this->success([
                'status'           => 'not_submitted',
                'is_verified'      => $user->is_verified,
                'id_type'          => null,
                'rejection_reason' => null,
                'reviewed_at'      => null,
                'submitted_at'     => null,
            ]);
        }

        return $this->success([
            'status'           => $verification->status,
            'is_verified'      => $user->is_verified,
            'id_type'          => $verification->id_type,
            'rejection_reason' => $verification->rejection_reason,
            'reviewed_at'      => $verification->reviewed_at?->toIso8601String(),
            'submitted_at'     => $verification->created_at?->toIso8601String(),
        ]);
    }

    /**
     * Submit identity verification documents.
     * Allowed when: no submission exists, or previous submission was rejected.
     */
    public function submit(SubmitVerificationRequest $request): JsonResponse
    {
        $user         = $request->user();
        $existing     = IdentityVerification::where('user_id', $user->id)->first();

        if ($existing && in_array($existing->status, ['pending', 'approved'])) {
            $msg = $existing->status === 'approved'
                ? 'تم التحقق من هويتك مسبقاً.'
                : 'طلبك قيد المراجعة، يرجى الانتظار.';

            return $this->error($msg, 422);
        }

        $frontPath = $this->storeDoc($request, 'document_front');
        $backPath  = $request->hasFile('document_back')
            ? $this->storeDoc($request, 'document_back')
            : null;
        $selfiePath = $request->hasFile('selfie')
            ? $this->storeDoc($request, 'selfie')
            : null;

        $data = [
            'user_id'              => $user->id,
            'id_type'              => $request->id_type,
            'document_front_path'  => $frontPath,
            'document_back_path'   => $backPath,
            'selfie_path'          => $selfiePath,
            'status'               => 'pending',
            'reviewed_by'          => null,
            'rejection_reason'     => null,
            'reviewed_at'          => null,
        ];

        if ($existing) {
            // Delete old files before replacing
            $this->deleteDocIfExists($existing->document_front_path);
            $this->deleteDocIfExists($existing->document_back_path);
            $this->deleteDocIfExists($existing->selfie_path);

            $existing->update($data);
        } else {
            IdentityVerification::create($data);
        }

        return $this->success([
            'status'       => 'pending',
            'is_verified'  => false,
            'submitted_at' => now()->toIso8601String(),
        ], 'تم إرسال طلب التحقق بنجاح. سيتم مراجعته خلال 24-48 ساعة.', 201);
    }

    private function storeDoc(Request $request, string $field): string
    {
        return $request->file($field)->store('verifications', 'local');
    }

    private function deleteDocIfExists(?string $path): void
    {
        if ($path && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
    }
}
