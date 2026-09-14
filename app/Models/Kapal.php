<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Kapal extends Model
{
    use HasFactory;

    protected $table = 'kapal';

    protected $fillable = [
        'kode_kapal',
        'nama_kapal',
        'nelayan_id',
        'jenis_kapal',
        'gt',
        'no_sipi',
        'is_active',
    ];

    protected $casts = [
        'gt' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function nelayan(): BelongsTo
    {
        return $this->belongsTo(Nelayan::class);
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
