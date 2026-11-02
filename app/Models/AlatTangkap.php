<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlatTangkap extends Model
{
    use HasFactory;

    protected $table = 'alat_tangkap';

    protected $fillable = [
        'kode_alat',
        'nama_alat',
        'jenis_kategori',
        'nelayan_id',
        'is_active',
    ];

    protected $casts = [
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
