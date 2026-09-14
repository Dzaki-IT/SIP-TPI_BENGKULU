<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Tpi extends Model
{
    use HasFactory;

    protected $table = 'tpi';

    protected $fillable = [
        'kode_tpi',
        'nama_tpi',
        'wilayah_id',
        'alamat',
        'instansi_pengelola',
        'jenis_perairan',
        'status_operasi',
        'keterangan',
    ];

    protected $casts = [
        'jenis_perairan' => 'string',
        'status_operasi' => 'string',
    ];

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function nelayan(): HasMany
    {
        return $this->hasMany(Nelayan::class);
    }

    public function pendaratan(): HasMany
    {
        return $this->hasMany(Pendaratan::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status_operasi', 'aktif');
    }
}
