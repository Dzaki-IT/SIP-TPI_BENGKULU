<?php

namespace Tests\Feature;

use App\Models\AlatTangkap;
use App\Models\JenisIkan;
use App\Models\Kapal;
use App\Models\Nelayan;
use App\Models\Tpi;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Wilayah $wilayah;

    protected function setUp(): void
    {
        parent::setUp();

        $this->wilayah = Wilayah::firstOrCreate(
            ['kode_wilayah' => '1771'],
            ['nama_kabupaten_kota' => 'Kota Bengkulu']
        );

        $this->user = User::firstOrCreate(
            ['email' => 'admin_test@tpi-bengkulu.test'],
            [
                'name' => 'Admin Tester',
                'password' => bcrypt('password'),
                'role' => 'admin_tpi',
                'is_active' => true,
            ]
        );
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/tpi')->assertRedirect('/login');
        $this->get('/nelayan')->assertRedirect('/login');
        $this->get('/kapal')->assertRedirect('/login');
        $this->get('/alat-tangkap')->assertRedirect('/login');
        $this->get('/jenis-ikan')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_master_indexes(): void
    {
        $this->actingAs($this->user);

        $this->get(route('tpi.index'))->assertOk()->assertSee('Data Tempat Pelelangan Ikan');
        $this->get(route('nelayan.index'))->assertOk()->assertSee('Data Nelayan Terdaftar');
        $this->get(route('kapal.index'))->assertOk()->assertSee('Data Armada Kapal');
        $this->get(route('alat-tangkap.index'))->assertOk()->assertSee('Data Alat Penangkapan Ikan');
        $this->get(route('jenis-ikan.index'))->assertOk()->assertSee('Master Komoditas Ikan');
    }

    public function test_tpi_crud_lifecycle(): void
    {
        $this->actingAs($this->user);

        // Create
        $response = $this->post(route('tpi.store'), [
            'kode_tpi' => 'TPI-TEST',
            'nama_tpi' => 'TPI Pantai Berkas',
            'wilayah_id' => $this->wilayah->id,
            'instansi_pengelola' => 'Dinas Kelautan',
            'jenis_perairan' => 'laut',
            'status_operasi' => 'aktif',
            'alamat' => 'Pantai Berkas, Kota Bengkulu',
            'keterangan' => 'Unit uji coba',
        ]);

        $response->assertRedirect(route('tpi.index'));
        $response->assertSessionHas('success');

        $tpi = Tpi::where('kode_tpi', 'TPI-TEST')->first();
        $this->assertNotNull($tpi);

        // Edit
        $this->get(route('tpi.edit', $tpi))->assertOk()->assertSee('Pantai Berkas');

        // Update
        $updateResponse = $this->put(route('tpi.update', $tpi), [
            'kode_tpi' => 'TPI-TEST',
            'nama_tpi' => 'TPI Pantai Berkas Baru',
            'wilayah_id' => $this->wilayah->id,
            'jenis_perairan' => 'laut',
            'status_operasi' => 'aktif',
        ]);

        $updateResponse->assertRedirect(route('tpi.index'));
        $this->assertEquals('TPI Pantai Berkas Baru', $tpi->fresh()->nama_tpi);

        // Delete
        $deleteResponse = $this->delete(route('tpi.destroy', $tpi));
        $deleteResponse->assertRedirect(route('tpi.index'));
        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('tpi', ['id' => $tpi->id]);
    }

    public function test_nelayan_crud_lifecycle(): void
    {
        $this->actingAs($this->user);

        $tpi = Tpi::firstOrCreate(
            ['kode_tpi' => 'TPI-DUMMY'],
            ['nama_tpi' => 'TPI Dummy', 'wilayah_id' => $this->wilayah->id, 'status_operasi' => 'aktif']
        );

        // Create
        $response = $this->post(route('nelayan.store'), [
            'kode_nelayan' => 'NL-TEST',
            'nama' => 'Pak Joko Nelayan',
            'nik' => '1771012345678901',
            'tpi_id' => $tpi->id,
            'kelompok_nelayan' => 'KUB Samudera',
            'alamat' => 'Bengkulu',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('nelayan.index'));
        $response->assertSessionHas('success');

        $nelayan = Nelayan::where('kode_nelayan', 'NL-TEST')->first();
        $this->assertNotNull($nelayan);

        // Edit
        $this->get(route('nelayan.edit', $nelayan))->assertOk()->assertSee('Pak Joko Nelayan');

        // Update
        $this->put(route('nelayan.update', $nelayan), [
            'kode_nelayan' => 'NL-TEST',
            'nama' => 'Pak Joko Nelayan Hebat',
            'nik' => '1771012345678901',
            'tpi_id' => $tpi->id,
            'is_active' => '1',
        ])->assertRedirect(route('nelayan.index'));

        $this->assertEquals('Pak Joko Nelayan Hebat', $nelayan->fresh()->nama);

        // Delete
        $this->delete(route('nelayan.destroy', $nelayan))
            ->assertRedirect(route('nelayan.index'))
            ->assertSessionHas('success');
        $this->assertDatabaseMissing('nelayan', ['id' => $nelayan->id]);
    }

    public function test_kapal_crud_lifecycle(): void
    {
        $this->actingAs($this->user);

        // Create
        $response = $this->post(route('kapal.store'), [
            'kode_kapal' => 'KPL-TEST',
            'nama_kapal' => 'KM Sumber Rezeki',
            'jenis_kapal' => 'motor_tempel',
            'gt' => 3.5,
            'no_sipi' => 'SIPI-BKL-TEST',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('kapal.index'));
        $response->assertSessionHas('success');

        $kapal = Kapal::where('kode_kapal', 'KPL-TEST')->first();
        $this->assertNotNull($kapal);

        // Edit
        $this->get(route('kapal.edit', $kapal))->assertOk()->assertSee('KM Sumber Rezeki');

        // Update
        $this->put(route('kapal.update', $kapal), [
            'kode_kapal' => 'KPL-TEST',
            'nama_kapal' => 'KM Sumber Rezeki Abadi',
            'jenis_kapal' => 'kapal_motor',
            'gt' => 5.0,
            'is_active' => '1',
        ])->assertRedirect(route('kapal.index'));

        $this->assertEquals('KM Sumber Rezeki Abadi', $kapal->fresh()->nama_kapal);

        // Delete
        $this->delete(route('kapal.destroy', $kapal))
            ->assertRedirect(route('kapal.index'))
            ->assertSessionHas('success');
        $this->assertDatabaseMissing('kapal', ['id' => $kapal->id]);
    }

    public function test_alat_tangkap_crud_lifecycle(): void
    {
        $this->actingAs($this->user);

        // Create
        $response = $this->post(route('alat-tangkap.store'), [
            'kode_alat' => 'AT-TEST',
            'nama_alat' => 'Jaring Insang Percobaan',
            'jenis_kategori' => 'Jaring',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('alat-tangkap.index'));
        $response->assertSessionHas('success');

        $alat = AlatTangkap::where('kode_alat', 'AT-TEST')->first();
        $this->assertNotNull($alat);

        // Edit
        $this->get(route('alat-tangkap.edit', $alat))->assertOk()->assertSee('Jaring Insang Percobaan');

        // Update
        $this->put(route('alat-tangkap.update', $alat), [
            'kode_alat' => 'AT-TEST',
            'nama_alat' => 'Jaring Insang Modern',
            'jenis_kategori' => 'Jaring',
            'is_active' => '1',
        ])->assertRedirect(route('alat-tangkap.index'));

        $this->assertEquals('Jaring Insang Modern', $alat->fresh()->nama_alat);

        // Delete
        $this->delete(route('alat-tangkap.destroy', $alat))
            ->assertRedirect(route('alat-tangkap.index'))
            ->assertSessionHas('success');
        $this->assertDatabaseMissing('alat_tangkap', ['id' => $alat->id]);
    }

    public function test_jenis_ikan_crud_lifecycle(): void
    {
        $this->actingAs($this->user);

        // Create
        $response = $this->post(route('jenis-ikan.store'), [
            'kode_ikan' => 'IKN-TEST',
            'nama_resmi' => 'Ikan Kurisi',
            'nama_lokal' => 'Nemipterus virgatus',
            'kategori' => 'Demersal',
            'satuan' => 'Kg',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('jenis-ikan.index'));
        $response->assertSessionHas('success');

        $ikan = JenisIkan::where('kode_ikan', 'IKN-TEST')->first();
        $this->assertNotNull($ikan);

        // Edit
        $this->get(route('jenis-ikan.edit', $ikan))->assertOk()->assertSee('Ikan Kurisi');

        // Update
        $this->put(route('jenis-ikan.update', $ikan), [
            'kode_ikan' => 'IKN-TEST',
            'nama_resmi' => 'Ikan Kurisi Merah',
            'nama_lokal' => 'Nemipterus virgatus',
            'kategori' => 'Demersal',
            'satuan' => 'Kg',
            'is_active' => '1',
        ])->assertRedirect(route('jenis-ikan.index'));

        $this->assertEquals('Ikan Kurisi Merah', $ikan->fresh()->nama_resmi);

        // Delete
        $this->delete(route('jenis-ikan.destroy', $ikan))
            ->assertRedirect(route('jenis-ikan.index'))
            ->assertSessionHas('success');
        $this->assertDatabaseMissing('jenis_ikan', ['id' => $ikan->id]);
    }

    public function test_prevent_delete_when_has_relations(): void
    {
        $this->actingAs($this->user);

        $tpi = Tpi::create([
            'kode_tpi' => 'TPI-REL',
            'nama_tpi' => 'TPI Relasional',
            'wilayah_id' => $this->wilayah->id,
            'jenis_perairan' => 'laut',
            'status_operasi' => 'aktif',
        ]);

        $nelayan = Nelayan::create([
            'kode_nelayan' => 'NL-REL',
            'nama' => 'Nelayan Relasional',
            'tpi_id' => $tpi->id,
            'is_active' => true,
        ]);

        $kapal = Kapal::create([
            'kode_kapal' => 'KPL-REL',
            'nama_kapal' => 'Kapal Relasional',
            'nelayan_id' => $nelayan->id,
            'jenis_kapal' => 'motor_tempel',
            'is_active' => true,
        ]);

        $alat = AlatTangkap::create([
            'kode_alat' => 'AT-REL',
            'nama_alat' => 'Alat Relasional',
            'is_active' => true,
        ]);

        $ikan = JenisIkan::create([
            'kode_ikan' => 'IKN-REL',
            'nama_resmi' => 'Ikan Relasional',
            'kategori' => 'Pelagis Kecil',
            'satuan' => 'Kg',
            'is_active' => true,
        ]);

        // TPI cannot be deleted because it has nelayan
        $this->delete(route('tpi.destroy', $tpi))
            ->assertRedirect(route('tpi.index'))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('tpi', ['id' => $tpi->id]);

        // Nelayan cannot be deleted because it has kapal
        $this->delete(route('nelayan.destroy', $nelayan))
            ->assertRedirect(route('nelayan.index'))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('nelayan', ['id' => $nelayan->id]);

        // Create Pendaratan transaction
        $pendaratan = \App\Models\Pendaratan::create([
            'tpi_id' => $tpi->id,
            'kapal_id' => $kapal->id,
            'nelayan_id' => $nelayan->id,
            'alat_tangkap_id' => $alat->id,
            'tanggal_pendaratan' => now()->toDateString(),
            'status_verifikasi' => 'draft',
            'created_by' => $this->user->id,
        ]);

        \App\Models\DetailPendaratan::create([
            'pendaratan_id' => $pendaratan->id,
            'jenis_ikan_id' => $ikan->id,
            'volume_kg' => 100,
            'harga_per_kg' => 25000,
        ]);

        // Kapal cannot be deleted because it has pendaratan
        $this->delete(route('kapal.destroy', $kapal))
            ->assertRedirect(route('kapal.index'))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('kapal', ['id' => $kapal->id]);

        // Alat tangkap cannot be deleted because it has pendaratan
        $this->delete(route('alat-tangkap.destroy', $alat))
            ->assertRedirect(route('alat-tangkap.index'))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('alat_tangkap', ['id' => $alat->id]);

        // Jenis ikan cannot be deleted because it has detail_pendaratan
        $this->delete(route('jenis-ikan.destroy', $ikan))
            ->assertRedirect(route('jenis-ikan.index'))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('jenis_ikan', ['id' => $ikan->id]);
    }
}
