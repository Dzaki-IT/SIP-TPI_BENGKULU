<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReportFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->canViewOperationalData() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'tab' => ['nullable', Rule::in(['tpi', 'ikan', 'dkp', 'triwulan'])],
            'tipe' => ['nullable', Rule::in(['tpi', 'ikan', 'dkp', 'triwulan'])],
            'tahun' => ['nullable', 'integer', 'between:2000,'.now()->year],
            'periode_tipe' => ['nullable', Rule::in(['semua', 'bulanan', 'triwulanan'])],
            'bulan' => ['nullable', 'integer', 'between:1,12', Rule::requiredIf($this->input('periode_tipe') === 'bulanan')],
            'triwulan' => ['nullable', 'integer', 'between:1,4', Rule::requiredIf($this->input('periode_tipe') === 'triwulanan')],
            'tpi_id' => ['nullable', 'integer', Rule::exists('tpi', 'id')],
            'kategori_ikan' => ['nullable', Rule::in(['Pelagis Besar', 'Pelagis Kecil', 'Demersal', 'Non-Ikan', 'Lainnya'])],
        ];
    }
}
