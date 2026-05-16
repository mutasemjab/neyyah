<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdentityVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'id_type',
        'document_front_path',
        'document_back_path',
        'selfie_path',
        'status',
        'reviewed_by',
        'rejection_reason',
        'reviewed_at',
    ];
    protected $hidden = ['document_front_path', 'document_back_path', 'selfie_path'];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approve(User $admin): void
    {
        $this->update(['status' => 'approved', 'reviewed_by' => $admin->id, 'reviewed_at' => now()]);
        $this->user->update(['is_verified' => true]);
    }

    public function reject(User $admin, string $reason): void
    {
        $this->update([
            'status'           => 'rejected',
            'reviewed_by'      => $admin->id,
            'reviewed_at'      => now(),
            'rejection_reason' => $reason,
        ]);
    }
}
