<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

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
