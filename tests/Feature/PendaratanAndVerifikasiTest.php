<?php

namespace Tests\Feature;

use App\Models\AlatTangkap;
use App\Models\DetailPendaratan;
use App\Models\JenisIkan;
use App\Models\Kapal;
use App\Models\Nelayan;
use App\Models\Pendaratan;
use App\Models\Tpi;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PendaratanAndVerifikasiTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminTpi;
    protected User $verifikator;
    protected User $pimpinan;
    protected Tpi $tpi;
    protected Nelayan $nelayan;
    protected Kapal $kapal;
    protected AlatTangkap $alatTangkap;
    protected JenisIkan $ikan1;
    protected JenisIkan $ikan2;

    protected function setUp(): void
    {
        parent::setUp();

        $wilayah = Wilayah::create([
            'kode_wilayah' => '1771',
            'nama_kabupaten_kota' => 'Kota Bengkulu',
        ]);

        $this->tpi = Tpi::create([
            'kode_tpi' => 'TPI-001',
            'nama_tpi' => 'TPI Pulau Baai',
            'wilayah_id' => $wilayah->id,
            'status_operasi' => 'aktif',
        ]);

        $this->adminTpi = User::create([
            'name' => 'Petugas TPI Pulau Baai',
            'email' => 'admin_baai@tpi-bengkulu.test',
            'password' => bcrypt('password'),
            'role' => 'admin_tpi',
            'tpi_id' => $this->tpi->id,
            'is_active' => true,
        ]);

        $this->verifikator = User::create([
            'name' => 'Verifikator DKP',
            'email' => 'verifikator_dkp@tpi-bengkulu.test',
            'password' => bcrypt('password'),
            'role' => 'verifikator',
            'tpi_id' => null,
            'is_active' => true,
        ]);

        $this->pimpinan = User::create([
            'name' => 'Kepala Dinas DKP',
            'email' => 'pimpinan_dkp@tpi-bengkulu.test',
            'password' => bcrypt('password'),
            'role' => 'pimpinan',
            'tpi_id' => null,
            'is_active' => true,
        ]);

        $this->nelayan = Nelayan::create([
            'kode_nelayan' => 'NL-001',
            'nama' => 'Pak Joko Nelayan',
            'tpi_id' => $this->tpi->id,
            'is_active' => true,
        ]);

        $this->kapal = Kapal::create([
            'kode_kapal' => 'KP-001',
            'nama_kapal' => 'KM Samudra 01',
            'nelayan_id' => $this->nelayan->id,
            'jenis_kapal' => 'kapal_motor',
            'gt' => 15.50,
            'is_active' => true,
        ]);

        $this->alatTangkap = AlatTangkap::create([
            'kode_alat' => 'API-001',
            'nama_alat' => 'Pukat Cincin (Purse Seine)',
            'jenis_kategori' => 'Jaring',
            'is_active' => true,
        ]);

        $this->ikan1 = JenisIkan::create([
            'kode_ikan' => 'IK-001',
            'nama_resmi' => 'Tongkol Komo',
            'nama_lokal' => 'Tongkol',
            'kategori' => 'Pelagis Kecil',
            'is_active' => true,
        ]);

        $this->ikan2 = JenisIkan::create([
            'kode_ikan' => 'IK-002',
            'nama_resmi' => 'Tenggiri Batang',
            'nama_lokal' => 'Tenggiri',
            'kategori' => 'Pelagis Besar',
            'is_active' => true,
        ]);
    }

    public function test_admin_tpi_can_access_pendaratan_index_and_create(): void
    {
        $this->actingAs($this->adminTpi);

        $this->get(route('pendaratan.index'))
            ->assertOk()
            ->assertSee('Data Pendaratan');

        $this->get(route('pendaratan.create'))
            ->assertOk()
            ->assertSee('Formulir Pencatatan Pendaratan Ikan')
            ->assertSee('TPI Pulau Baai');
    }

    public function test_admin_tpi_can_store_landing_as_draft_with_multiple_items(): void
    {
        $this->actingAs($this->adminTpi);

        $postData = [
            'tpi_id' => $this->tpi->id,
            'tanggal_pendaratan' => now()->format('Y-m-d'),
            'kapal_id' => $this->kapal->id,
            'nelayan_id' => $this->nelayan->id,
            'alat_tangkap_id' => $this->alatTangkap->id,
            'action' => 'draft',
            'items' => [
                [
                    'jenis_ikan_id' => $this->ikan1->id,
                    'volume_kg' => 100.50,
                    'harga_per_kg' => 25000,
                    'keterangan' => 'Mutu A Segar',
                ],
                [
                    'jenis_ikan_id' => $this->ikan2->id,
                    'volume_kg' => 50.00,
                    'harga_per_kg' => 60000,
                    'keterangan' => 'Mutu Super',
                ],
            ],
        ];

        $response = $this->post(route('pendaratan.store'), $postData);

        $pendaratan = Pendaratan::first();
        $this->assertNotNull($pendaratan);
        $this->assertEquals('draft', $pendaratan->status_verifikasi);
        $this->assertEquals($this->tpi->id, $pendaratan->tpi_id);
        $this->assertEquals($this->adminTpi->id, $pendaratan->created_by);
        $this->assertStringStartsWith('PDR-', $pendaratan->kode_pendaratan);

        // Check auto-calculation of nilai_produksi on details
        $this->assertCount(2, $pendaratan->detailPendaratan);

        $detail1 = $pendaratan->detailPendaratan()->where('jenis_ikan_id', $this->ikan1->id)->first();
        $this->assertEquals(100.50, $detail1->volume_kg);
        $this->assertEquals(25000, $detail1->harga_per_kg);
        $this->assertEquals(100.50 * 25000, (float) $detail1->nilai_produksi);

        $detail2 = $pendaratan->detailPendaratan()->where('jenis_ikan_id', $this->ikan2->id)->first();
        $this->assertEquals(50.00, $detail2->volume_kg);
        $this->assertEquals(60000, $detail2->harga_per_kg);
        $this->assertEquals(50.00 * 60000, (float) $detail2->nilai_produksi);

        $response->assertRedirect(route('pendaratan.show', $pendaratan));
    }

    public function test_admin_tpi_can_store_landing_and_submit_directly(): void
    {
        $this->actingAs($this->adminTpi);

        $postData = [
            'tpi_id' => $this->tpi->id,
            'tanggal_pendaratan' => now()->format('Y-m-d'),
            'kapal_id' => $this->kapal->id,
            'nelayan_id' => $this->nelayan->id,
            'alat_tangkap_id' => $this->alatTangkap->id,
            'action' => 'diajukan',
            'items' => [
                [
                    'jenis_ikan_id' => $this->ikan1->id,
                    'volume_kg' => 200,
                    'harga_per_kg' => 20000,
                    'keterangan' => 'Lelang TPI',
                ],
            ],
        ];

        $response = $this->post(route('pendaratan.store'), $postData);

        $pendaratan = Pendaratan::first();
        $this->assertEquals('diajukan', $pendaratan->status_verifikasi);
        $response->assertRedirect(route('pendaratan.show', $pendaratan));
    }

    public function test_admin_tpi_can_submit_draft_landing_for_verification(): void
    {
        $this->actingAs($this->adminTpi);

        $pendaratan = Pendaratan::create([
            'tpi_id' => $this->tpi->id,
            'tanggal_pendaratan' => now()->format('Y-m-d'),
            'kapal_id' => $this->kapal->id,
            'status_verifikasi' => 'draft',
            'created_by' => $this->adminTpi->id,
        ]);

        $response = $this->post(route('pendaratan.ajukan', $pendaratan));

        $pendaratan->refresh();
        $this->assertEquals('diajukan', $pendaratan->status_verifikasi);
        $response->assertSessionHas('success');
    }

    public function test_verifikator_can_approve_landing(): void
    {
        $pendaratan = Pendaratan::create([
            'tpi_id' => $this->tpi->id,
            'tanggal_pendaratan' => now()->format('Y-m-d'),
            'kapal_id' => $this->kapal->id,
            'status_verifikasi' => 'diajukan',
            'created_by' => $this->adminTpi->id,
        ]);

        $this->actingAs($this->verifikator);

        $response = $this->post(route('verifikasi.approve', $pendaratan), [
            'catatan' => 'Semua data dan nota lelang telah sesuai.',
        ]);

        $pendaratan->refresh();
        $this->assertEquals('diverifikasi', $pendaratan->status_verifikasi);
        $this->assertEquals($this->verifikator->id, $pendaratan->verified_by);
        $this->assertNotNull($pendaratan->verified_at);
        $this->assertEquals('Semua data dan nota lelang telah sesuai.', $pendaratan->catatan_verifikasi);

        $response->assertRedirect(route('verifikasi.index'));
    }

    public function test_verifikator_can_reject_landing_with_notes(): void
    {
        $pendaratan = Pendaratan::create([
            'tpi_id' => $this->tpi->id,
            'tanggal_pendaratan' => now()->format('Y-m-d'),
            'kapal_id' => $this->kapal->id,
            'status_verifikasi' => 'diajukan',
            'created_by' => $this->adminTpi->id,
        ]);

        $this->actingAs($this->verifikator);

        $response = $this->post(route('verifikasi.reject', $pendaratan), [
            'catatan_verifikasi' => 'Volume tongkol melebihi kapasitas palka, mohon periksa ulang.',
        ]);

        $pendaratan->refresh();
        $this->assertEquals('ditolak', $pendaratan->status_verifikasi);
        $this->assertEquals($this->verifikator->id, $pendaratan->verified_by);
        $this->assertNotNull($pendaratan->verified_at);
        $this->assertEquals('Volume tongkol melebihi kapasitas palka, mohon periksa ulang.', $pendaratan->catatan_verifikasi);

        $response->assertRedirect(route('verifikasi.index'));
    }

    public function test_rejection_requires_notes_of_min_length(): void
    {
        $pendaratan = Pendaratan::create([
            'tpi_id' => $this->tpi->id,
            'tanggal_pendaratan' => now()->format('Y-m-d'),
            'status_verifikasi' => 'diajukan',
            'created_by' => $this->adminTpi->id,
        ]);

        $this->actingAs($this->verifikator);

        $response = $this->post(route('verifikasi.reject', $pendaratan), [
            'catatan_verifikasi' => 'abc', // < 5 chars
        ]);

        $response->assertSessionHasErrors('catatan_verifikasi');
        $pendaratan->refresh();
        $this->assertEquals('diajukan', $pendaratan->status_verifikasi);
    }

    public function test_unauthorized_user_cannot_access_or_approve_verification(): void
    {
        $pendaratan = Pendaratan::create([
            'tpi_id' => $this->tpi->id,
            'tanggal_pendaratan' => now()->format('Y-m-d'),
            'status_verifikasi' => 'diajukan',
            'created_by' => $this->adminTpi->id,
        ]);

        // Regular admin_tpi cannot access verifikasi routes
        $this->actingAs($this->adminTpi);

        $this->get(route('verifikasi.index'))->assertForbidden();
        $this->post(route('verifikasi.approve', $pendaratan))->assertForbidden();
        $this->post(route('verifikasi.reject', $pendaratan), ['catatan_verifikasi' => 'Tolak'])->assertForbidden();

        // Pimpinan can view verifikasi index but cannot approve/reject
        $this->actingAs($this->pimpinan);
        $this->get(route('verifikasi.index'))->assertOk();
        $this->post(route('verifikasi.approve', $pendaratan))->assertForbidden();
        $this->post(route('verifikasi.reject', $pendaratan), ['catatan_verifikasi' => 'Catatan revisi'])->assertForbidden();
    }

    public function test_verified_landing_cannot_be_edited_or_deleted(): void
    {
        $pendaratan = Pendaratan::create([
            'tpi_id' => $this->tpi->id,
            'tanggal_pendaratan' => now()->format('Y-m-d'),
            'status_verifikasi' => 'diverifikasi',
            'verified_by' => $this->verifikator->id,
            'verified_at' => now(),
            'created_by' => $this->adminTpi->id,
        ]);

        $this->actingAs($this->adminTpi);

        // Edit form should redirect with error
        $this->get(route('pendaratan.edit', $pendaratan))
            ->assertRedirect(route('pendaratan.show', $pendaratan))
            ->assertSessionHas('error');

        // Update should redirect with error
        $this->put(route('pendaratan.update', $pendaratan), [
            'tpi_id' => $this->tpi->id,
            'tanggal_pendaratan' => now()->format('Y-m-d'),
            'action' => 'draft',
            'items' => [
                [
                    'jenis_ikan_id' => $this->ikan1->id,
                    'volume_kg' => 50,
                ],
            ],
        ])->assertRedirect(route('pendaratan.show', $pendaratan))
          ->assertSessionHas('error');

        // Delete should redirect with error
        $this->delete(route('pendaratan.destroy', $pendaratan))
            ->assertRedirect(route('pendaratan.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('pendaratan', ['id' => $pendaratan->id]);
    }

    public function test_draft_landing_can_be_deleted(): void
    {
        $pendaratan = Pendaratan::create([
            'tpi_id' => $this->tpi->id,
            'tanggal_pendaratan' => now()->format('Y-m-d'),
            'status_verifikasi' => 'draft',
            'created_by' => $this->adminTpi->id,
        ]);

        $detail = DetailPendaratan::create([
            'pendaratan_id' => $pendaratan->id,
            'jenis_ikan_id' => $this->ikan1->id,
            'volume_kg' => 10,
            'harga_per_kg' => 15000,
        ]);

        $this->actingAs($this->adminTpi);

        $response = $this->delete(route('pendaratan.destroy', $pendaratan));

        $response->assertRedirect(route('pendaratan.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('pendaratan', ['id' => $pendaratan->id]);
        $this->assertDatabaseMissing('detail_pendaratan', ['id' => $detail->id]);
    }

    public function test_rejected_landing_can_be_edited_and_resubmitted(): void
    {
        $pendaratan = Pendaratan::create([
            'tpi_id' => $this->tpi->id,
            'tanggal_pendaratan' => now()->format('Y-m-d'),
            'status_verifikasi' => 'ditolak',
            'catatan_verifikasi' => 'Mohon perbaiki volume ikan tongkol',
            'verified_by' => $this->verifikator->id,
            'created_by' => $this->adminTpi->id,
        ]);

        $this->actingAs($this->adminTpi);

        // Can access edit page
        $this->get(route('pendaratan.edit', $pendaratan))
            ->assertOk()
            ->assertSee('Mohon perbaiki volume ikan tongkol');

        // Can update and resubmit
        $response = $this->put(route('pendaratan.update', $pendaratan), [
            'tpi_id' => $this->tpi->id,
            'tanggal_pendaratan' => now()->format('Y-m-d'),
            'action' => 'diajukan',
            'items' => [
                [
                    'jenis_ikan_id' => $this->ikan1->id,
                    'volume_kg' => 150,
                    'harga_per_kg' => 22000,
                ],
            ],
        ]);

        $pendaratan->refresh();
        $this->assertEquals('diajukan', $pendaratan->status_verifikasi);
        $this->assertEquals(150, $pendaratan->detailPendaratan->first()->volume_kg);
        $response->assertRedirect(route('pendaratan.show', $pendaratan));
    }
}
