<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class JenisIkan extends Model
{
    use HasFactory;

    protected $table = 'jenis_ikan';

    protected $fillable = [
        'kode_ikan',
        'nama_resmi',
        'nama_lokal',
        'kategori',
        'satuan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function detailPendaratan(): HasMany
    {
        return $this->hasMany(DetailPendaratan::class);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
