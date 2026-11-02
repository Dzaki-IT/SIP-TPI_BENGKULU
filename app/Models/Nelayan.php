<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nelayan extends Model
{
    use HasFactory;

    protected $table = 'nelayan';

    protected $fillable = [
        'kode_nelayan',
        'nama',
        'nik',
        'alamat',
        'kelompok_nelayan',
        'tpi_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tpi(): BelongsTo
    {
        return $this->belongsTo(Tpi::class);
    }

    public function kapal(): HasMany
    {
        return $this->hasMany(Kapal::class);
    }

    public function alatTangkap(): HasMany
    {
        return $this->hasMany(AlatTangkap::class);
    }

    public function pendaratan(): HasMany
    {
        return $this->hasMany(Pendaratan::class);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
