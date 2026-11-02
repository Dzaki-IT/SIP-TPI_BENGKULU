<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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

    protected static function booted(): void
    {
        static::creating(function (self $pendaratan): void {
            if (blank($pendaratan->kode_pendaratan)) {
                $pendaratan->kode_pendaratan = self::nextKodePendaratan($pendaratan->tanggal_pendaratan);
            }
        });
    }

    private static function nextKodePendaratan(string|Carbon|null $tanggalPendaratan): string
    {
        $tanggal = Carbon::parse($tanggalPendaratan ?? now())->toDateString();
        $tanggalKode = Carbon::parse($tanggal)->format('Ymd');

        return DB::transaction(function () use ($tanggal, $tanggalKode): string {
            DB::table('landing_number_sequences')->insertOrIgnore([
                'sequence_date' => $tanggal,
                'last_number' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $lastNumber = (int) DB::table('landing_number_sequences')
                ->where('sequence_date', $tanggal)
                ->lockForUpdate()
                ->value('last_number');
            $nextNumber = $lastNumber + 1;

            DB::table('landing_number_sequences')
                ->where('sequence_date', $tanggal)
                ->update([
                    'last_number' => $nextNumber,
                    'updated_at' => now(),
                ]);

            return sprintf('PDR-%s-%04d', $tanggalKode, $nextNumber);
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

    public function verificationHistories(): HasMany
    {
        return $this->hasMany(VerificationHistory::class)->latest('id');
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
