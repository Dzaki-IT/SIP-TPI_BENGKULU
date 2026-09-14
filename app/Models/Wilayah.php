<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wilayah extends Model
{
    use HasFactory;

    protected $table = 'wilayah';

    protected $fillable = [
        'kode_wilayah',
        'nama_kabupaten_kota',
    ];

    /**
     * Get the TPIs for the wilayah.
     */
    public function tpi(): HasMany
    {
        return $this->hasMany(Tpi::class);
    }
}
