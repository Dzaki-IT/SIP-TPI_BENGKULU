<?php

namespace Tests\Feature;

use App\Models\Nelayan;
use App\Models\Tpi;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_pimpinan_can_create_an_admin_tpi_that_can_log_in_and_only_see_assigned_tpi_data(): void
    {
        $context = $this->accountContext();

        $this->actingAs($context['pimpinan'])
            ->post(route('akun.store'), [
                'name' => 'Admin TPI Muko Muko',
                'email' => 'admin.mukomuko@example.test',
                'role' => 'admin_tpi',
                'tpi_id' => $context['assignedTpi']->id,
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
            ])
            ->assertRedirect(route('akun.index'));

        $adminTpi = User::where('email', 'admin.mukomuko@example.test')->firstOrFail();

        $this->assertDatabaseHas('users', [
            'id' => $adminTpi->id,
            'name' => 'Admin TPI Muko Muko',
            'role' => 'admin_tpi',
            'tpi_id' => $context['assignedTpi']->id,
        ]);

        $this->post(route('logout'))->assertRedirect(route('login'));

        $this->post(route('login'), [
            'email' => 'admin.mukomuko@example.test',
            'password' => 'rahasia123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($adminTpi);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Selamat Datang, Admin TPI Muko Muko!')
            ->assertSee($context['assignedTpi']->nama_tpi);

        $this->get(route('nelayan.index'))
            ->assertOk()
            ->assertSee($context['assignedNelayan']->nama)
            ->assertDontSee($context['otherNelayan']->nama);
    }

    public function test_admin_tpi_account_requires_an_active_tpi_assignment(): void
    {
        $context = $this->accountContext();

        $this->actingAs($context['pimpinan'])
            ->from(route('akun.create'))
            ->post(route('akun.store'), [
                'name' => 'Admin Tanpa TPI',
                'email' => 'tanpa-tpi@example.test',
                'role' => 'admin_tpi',
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
            ])
            ->assertRedirect(route('akun.create'))
            ->assertSessionHasErrors('tpi_id');
    }

    public function test_pimpinan_can_update_account_credentials_and_role(): void
    {
        $context = $this->accountContext();
        $account = User::create([
            'name' => 'Admin Lama',
            'email' => 'admin.lama@example.test',
            'password' => 'passwordlama',
            'role' => 'admin_tpi',
            'tpi_id' => $context['assignedTpi']->id,
            'is_active' => true,
        ]);

        $this->actingAs($context['pimpinan'])
            ->put(route('akun.update', $account), [
                'name' => 'Verifikator Baru',
                'email' => 'verifikator.baru@example.test',
                'role' => 'verifikator',
                'is_active' => true,
                'password' => 'passwordbaru',
                'password_confirmation' => 'passwordbaru',
            ])
            ->assertRedirect(route('akun.index'));

        $account->refresh();

        $this->assertSame('Verifikator Baru', $account->name);
        $this->assertSame('verifikator', $account->role);
        $this->assertNull($account->tpi_id);
        $this->assertTrue(Hash::check('passwordbaru', $account->password));
    }

    public function test_only_pimpinan_can_access_account_management(): void
    {
        $context = $this->accountContext();
        $verifikator = User::create([
            'name' => 'Verifikator',
            'email' => 'verifikator@example.test',
            'password' => 'password',
            'role' => 'verifikator',
            'is_active' => true,
        ]);

        $this->actingAs($verifikator)
            ->get(route('akun.index'))
            ->assertForbidden();

        $this->actingAs(User::create([
            'name' => 'Admin TPI',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin_tpi',
            'tpi_id' => $context['assignedTpi']->id,
            'is_active' => true,
        ]))
            ->get(route('akun.index'))
            ->assertForbidden();
    }

    public function test_pimpinan_cannot_deactivate_or_change_the_only_active_pimpinan_account(): void
    {
        $context = $this->accountContext();

        $this->actingAs($context['pimpinan'])
            ->from(route('akun.edit', $context['pimpinan']))
            ->put(route('akun.update', $context['pimpinan']), [
                'name' => $context['pimpinan']->name,
                'email' => $context['pimpinan']->email,
                'role' => 'pimpinan',
                'is_active' => false,
            ])
            ->assertRedirect(route('akun.edit', $context['pimpinan']))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $context['pimpinan']->id, 'is_active' => true]);
    }

    public function test_pimpinan_can_deactivate_and_reactivate_an_account_without_deleting_it(): void
    {
        $context = $this->accountContext();
        $account = User::create([
            'name' => 'Verifikator Dinonaktifkan',
            'email' => 'hapus@example.test',
            'password' => 'password',
            'role' => 'verifikator',
            'is_active' => true,
        ]);

        $this->actingAs($context['pimpinan'])
            ->put(route('akun.update', $account), [
                'name' => $account->name,
                'email' => $account->email,
                'role' => 'verifikator',
                'is_active' => false,
            ])
            ->assertRedirect(route('akun.index'));

        $this->assertDatabaseHas('users', ['id' => $account->id, 'is_active' => false]);

        $this->post(route('logout'));
        $this->from(route('login'))
            ->post(route('login'), ['email' => $account->email, 'password' => 'password'])
            ->assertRedirect(route('login'));

        $this->actingAs($context['pimpinan'])
            ->put(route('akun.update', $account), [
                'name' => $account->name,
                'email' => $account->email,
                'role' => 'verifikator',
                'is_active' => true,
            ])
            ->assertRedirect(route('akun.index'));

        $this->assertDatabaseHas('users', ['id' => $account->id, 'is_active' => true]);
    }

    /**
     * @return array{pimpinan: User, assignedTpi: Tpi, assignedNelayan: Nelayan, otherNelayan: Nelayan}
     */
    private function accountContext(): array
    {
        $wilayah = Wilayah::create([
            'kode_wilayah' => '1706',
            'nama_kabupaten_kota' => 'Mukomuko',
        ]);

        $assignedTpi = Tpi::create([
            'kode_tpi' => 'TPI-MUKO',
            'nama_tpi' => 'TPI Muko Muko',
            'wilayah_id' => $wilayah->id,
            'status_operasi' => 'aktif',
        ]);

        $otherTpi = Tpi::create([
            'kode_tpi' => 'TPI-LAIN',
            'nama_tpi' => 'TPI Lain',
            'wilayah_id' => $wilayah->id,
            'status_operasi' => 'aktif',
        ]);

        $assignedNelayan = Nelayan::create([
            'kode_nelayan' => 'NL-MUKO',
            'nama' => 'Nelayan Muko Muko',
            'tpi_id' => $assignedTpi->id,
            'is_active' => true,
        ]);

        $otherNelayan = Nelayan::create([
            'kode_nelayan' => 'NL-LAIN',
            'nama' => 'Nelayan TPI Lain',
            'tpi_id' => $otherTpi->id,
            'is_active' => true,
        ]);

        $pimpinan = User::create([
            'name' => 'Pimpinan',
            'email' => 'pimpinan@example.test',
            'password' => 'password',
            'role' => 'pimpinan',
            'is_active' => true,
        ]);

        return compact('pimpinan', 'assignedTpi', 'assignedNelayan', 'otherNelayan');
    }
}
