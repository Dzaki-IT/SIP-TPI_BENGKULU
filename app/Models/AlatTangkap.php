<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class AlatTangkap extends Model
{
    use HasFactory;

    protected $table = 'alat_tangkap';

    protected $fillable = [
        'kode_alat',
        'nama_alat',
        'jenis_kategori',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function pendaratan(): HasMany
    {
        return $this->hasMany(Pendaratan::class);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
