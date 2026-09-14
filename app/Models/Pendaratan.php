<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Pendaratan extends Model
{
    use HasFactory;

    protected $table = 'pendaratan';

    protected $fillable = [
        'kode_pendaratan',
        'tpi_id',
        'tanggal_pendaratan',
        'kapal_id',
        'nelayan_id',
        'alat_tangkap_id',
        'status_verifikasi',
        'verified_by',
        'verified_at',
        'catatan_verifikasi',
        'created_by',
    ];

    protected $casts = [
        'tanggal_pendaratan' => 'date',
        'verified_at' => 'datetime',
        'status_verifikasi' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->kode_pendaratan)) {
                $date = now()->format('Ymd');
                $latest = static::where('kode_pendaratan', 'like', "PDR-{$date}-%")->orderBy('id', 'desc')->first();
                $sequence = $latest ? ((int) substr($latest->kode_pendaratan, -4)) + 1 : 1;
                $model->kode_pendaratan = "PDR-{$date}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function tpi(): BelongsTo
    {
        return $this->belongsTo(Tpi::class);
    }

    public function kapal(): BelongsTo
    {
        return $this->belongsTo(Kapal::class);
    }

    public function nelayan(): BelongsTo
    {
        return $this->belongsTo(Nelayan::class);
    }

    public function alatTangkap(): BelongsTo
    {
        return $this->belongsTo(AlatTangkap::class);
    }

    public function detailPendaratan(): HasMany
    {
        return $this->hasMany(DetailPendaratan::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getTotalVolumeAttribute()
    {
        return $this->detailPendaratan->sum('volume_kg');
    }

    public function getTotalNilaiAttribute()
    {
        return $this->detailPendaratan->sum('nilai_produksi');
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status_verifikasi', 'draft');
    }

    public function scopeDiajukan(Builder $query): Builder
    {
        return $query->where('status_verifikasi', 'diajukan');
    }

    public function scopeDiverifikasi(Builder $query): Builder
    {
        return $query->where('status_verifikasi', 'diverifikasi');
    }

    public function scopeDitolak(Builder $query): Builder
    {
        return $query->where('status_verifikasi', 'ditolak');
    }
}
