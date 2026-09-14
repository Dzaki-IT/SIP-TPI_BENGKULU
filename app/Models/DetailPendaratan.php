<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPendaratan extends Model
{
    use HasFactory;

    protected $table = 'detail_pendaratan';

    protected $fillable = [
        'pendaratan_id',
        'jenis_ikan_id',
        'volume_kg',
        'harga_per_kg',
        'nilai_produksi',
        'keterangan',
    ];

    protected $casts = [
        'volume_kg' => 'decimal:2',
        'harga_per_kg' => 'decimal:2',
        'nilai_produksi' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (!empty($model->volume_kg) && !empty($model->harga_per_kg)) {
                $model->nilai_produksi = $model->volume_kg * $model->harga_per_kg;
            }
        });
    }

    public function pendaratan(): BelongsTo
    {
        return $this->belongsTo(Pendaratan::class);
    }

    public function jenisIkan(): BelongsTo
    {
        return $this->belongsTo(JenisIkan::class);
    }
}
