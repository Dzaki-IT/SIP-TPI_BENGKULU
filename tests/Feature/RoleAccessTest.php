<?php

namespace Tests\Feature;

use App\Models\Nelayan;
use App\Models\Pendaratan;
use App\Models\Tpi;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{0: string}>
     */
    public static function readOnlyRoles(): array
    {
        return [
            'verifikator' => ['verifikator'],
            'pimpinan' => ['pimpinan'],
        ];
    }

    #[DataProvider('readOnlyRoles')]
    public function test_read_only_roles_cannot_create_master_data(string $role): void
    {
        $context = $this->accessContext();

        $this->actingAs($context[$role])
            ->post(route('nelayan.store'), [])
            ->assertForbidden();
    }

    public function test_read_only_roles_see_master_data_without_management_controls(): void
    {
        $context = $this->accessContext();

        $this->actingAs($context['verifikator'])
            ->get(route('nelayan.index'))
            ->assertOk()
            ->assertSee('Data Nelayan Terdaftar')
            ->assertDontSee('Tambah Nelayan Baru')
            ->assertDontSee('Aksi');

        $this->actingAs($context['pimpinan'])
            ->get(route('jenis-ikan.index'))
            ->assertOk()
            ->assertSee('Master Komoditas Ikan')
            ->assertSee('Tambah Jenis Ikan')
            ->assertSee('Aksi')
            ->assertDontSee('Verifikasi Data');

        $this->actingAs($context['pimpinan'])
            ->get(route('verifikasi.index'))
            ->assertForbidden();
    }

    public function test_only_pimpinan_can_manage_tpi_data(): void
    {
        $context = $this->accessContext();

        $this->actingAs($context['adminTpi'])
            ->get(route('tpi.index'))
            ->assertOk()
            ->assertDontSee('Tambah TPI Baru')
            ->assertDontSee('Aksi');

        $this->actingAs($context['adminTpi'])
            ->get(route('tpi.create'))
            ->assertForbidden();

        $this->actingAs($context['adminTpi'])
            ->post(route('tpi.store'), [])
            ->assertForbidden();

        $this->actingAs($context['adminTpi'])
            ->get(route('tpi.edit', $context['assignedTpi']))
            ->assertForbidden();

        $this->actingAs($context['adminTpi'])
            ->delete(route('tpi.destroy', $context['assignedTpi']))
            ->assertForbidden();

        $this->actingAs($context['pimpinan'])
            ->get(route('tpi.index'))
            ->assertOk()
            ->assertSee('Tambah TPI Baru')
            ->assertSee('Aksi');

        $this->actingAs($context['pimpinan'])
            ->get(route('tpi.create'))
            ->assertOk();
    }

    public function test_admin_tpi_cannot_manage_data_belonging_to_another_tpi(): void
    {
        $context = $this->accessContext();

        $this->actingAs($context['adminTpi'])
            ->get(route('nelayan.edit', $context['otherNelayan']))
            ->assertForbidden();

        $this->actingAs($context['adminTpi'])
            ->put(route('nelayan.update', $context['otherNelayan']), [
                'kode_nelayan' => $context['otherNelayan']->kode_nelayan,
                'nama' => 'Tidak Boleh Berubah',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('nelayan', [
            'id' => $context['otherNelayan']->id,
            'nama' => $context['otherNelayan']->nama,
        ]);
    }

    public function test_only_verifikator_can_decide_a_submitted_landing(): void
    {
        $context = $this->accessContext();
        $pendaratan = Pendaratan::create([
            'tpi_id' => $context['assignedTpi']->id,
            'tanggal_pendaratan' => now()->toDateString(),
            'status_verifikasi' => 'diajukan',
            'created_by' => $context['adminTpi']->id,
        ]);

        $this->actingAs($context['pimpinan'])
            ->post(route('verifikasi.approve', $pendaratan))
            ->assertForbidden();

        $this->actingAs($context['verifikator'])
            ->post(route('verifikasi.approve', $pendaratan), ['catatan' => 'Data sesuai.'])
            ->assertRedirect(route('verifikasi.index'));

        $this->assertDatabaseHas('pendaratan', [
            'id' => $pendaratan->id,
            'status_verifikasi' => 'diverifikasi',
            'verified_by' => $context['verifikator']->id,
        ]);
    }

    /**
     * @return array{adminTpi: User, verifikator: User, pimpinan: User, assignedTpi: Tpi, otherNelayan: Nelayan}
     */
    private function accessContext(): array
    {
        $wilayah = Wilayah::create([
            'kode_wilayah' => '1771',
            'nama_kabupaten_kota' => 'Kota Bengkulu',
        ]);

        $assignedTpi = Tpi::create([
            'kode_tpi' => 'TPI-001',
            'nama_tpi' => 'TPI Pulau Baai',
            'wilayah_id' => $wilayah->id,
            'status_operasi' => 'aktif',
        ]);

        $otherTpi = Tpi::create([
            'kode_tpi' => 'TPI-002',
            'nama_tpi' => 'TPI Bina Karya',
            'wilayah_id' => $wilayah->id,
            'status_operasi' => 'aktif',
        ]);

        $adminTpi = User::create([
            'name' => 'Admin TPI',
            'email' => 'admin-tpi@example.test',
            'password' => 'password',
            'role' => 'admin_tpi',
            'tpi_id' => $assignedTpi->id,
            'is_active' => true,
        ]);

        $verifikator = User::create([
            'name' => 'Verifikator',
            'email' => 'verifikator@example.test',
            'password' => 'password',
            'role' => 'verifikator',
            'is_active' => true,
        ]);

        $pimpinan = User::create([
            'name' => 'Pimpinan',
            'email' => 'pimpinan@example.test',
            'password' => 'password',
            'role' => 'pimpinan',
            'is_active' => true,
        ]);

        $otherNelayan = Nelayan::create([
            'kode_nelayan' => 'NL-002',
            'nama' => 'Nelayan TPI Lain',
            'tpi_id' => $otherTpi->id,
            'is_active' => true,
        ]);

        return compact('adminTpi', 'verifikator', 'pimpinan', 'assignedTpi', 'otherNelayan');
    }
}
