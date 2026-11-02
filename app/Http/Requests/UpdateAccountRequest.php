<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('akun')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('akun'))],
            'role' => ['required', Rule::in(['admin_tpi', 'verifikator', 'pimpinan'])],
            'tpi_id' => [Rule::requiredIf($this->input('role') === 'admin_tpi'), 'nullable', Rule::exists('tpi', 'id')->where('status_operasi', 'aktif')],
            'is_active' => ['required', 'boolean'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Get custom validation messages for the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama akun wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email tersebut sudah digunakan.',
            'role.required' => 'Role akun wajib dipilih.',
            'role.in' => 'Role akun tidak valid.',
            'tpi_id.required' => 'Admin TPI wajib ditugaskan ke salah satu TPI.',
            'tpi_id.exists' => 'TPI yang dipilih tidak valid.',
            'is_active.required' => 'Status akun wajib dipilih.',
            'is_active.boolean' => 'Status akun tidak valid.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ];
    }
}
