<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'tpi_id', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the TPI associated with the user.
     */
    public function tpi(): BelongsTo
    {
        return $this->belongsTo(Tpi::class);
    }

    /**
     * Check if user is an admin or admin TPI.
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'admin_tpi']);
    }

    /**
     * Check if user is an admin TPI.
     */
    public function isAdminTpi(): bool
    {
        return $this->role === 'admin_tpi';
    }

    /**
     * Check if user is a verifikator.
     */
    public function isVerifikator(): bool
    {
        return $this->role === 'verifikator';
    }

    /**
     * Check if user is a pimpinan.
     */
    public function isPimpinan(): bool
    {
        return $this->role === 'pimpinan';
    }

    /**
     * Determine whether the user may view operational and master data.
     */
    public function canViewOperationalData(): bool
    {
        return in_array($this->role, ['admin', 'admin_tpi', 'verifikator', 'pimpinan'], true);
    }

    /**
     * Determine whether the user may manage central reference data.
     */
    public function canManageReferenceData(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Determine whether the user may manage data assigned to a TPI.
     */
    public function canManageAssignedTpiData(): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        return $this->isAdminTpi() && $this->tpi_id !== null;
    }

    /**
     * Determine whether the user may manage data for a specific TPI.
     */
    public function canManageTpiData(int $tpiId): bool
    {
        return $this->role === 'admin' || ($this->isAdminTpi() && $this->tpi_id === $tpiId);
    }

    /**
     * Determine whether the user may access the verification workspace.
     */
    public function canViewVerification(): bool
    {
        return $this->isVerifikator();
    }

    /**
     * Determine whether the user may decide a landing verification.
     */
    public function canVerifyPendaratan(): bool
    {
        return $this->isVerifikator();
    }

    /**
     * Get user-friendly role label.
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin_tpi' => 'Admin TPI',
            'verifikator' => 'Verifikator DKP',
            'pimpinan' => 'Pimpinan DKP',
            default => ucfirst(str_replace('_', ' ', (string) $this->role)),
        };
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
