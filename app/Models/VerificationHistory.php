<?php

namespace App\Models;

use Database\Factories\VerificationHistoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationHistory extends Model
{
    /** @use HasFactory<VerificationHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'pendaratan_id',
        'actor_id',
        'action',
        'from_status',
        'to_status',
        'catatan',
    ];

    public function pendaratan(): BelongsTo
    {
        return $this->belongsTo(Pendaratan::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
