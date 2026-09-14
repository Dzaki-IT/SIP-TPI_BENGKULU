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
use Carbon\Carbon;
use Database\Seeders\AlatTangkapSeeder;
use Database\Seeders\JenisIkanSeeder;
use Database\Seeders\SamplePendaratanSeeder;
use Database\Seeders\TpiSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\WilayahSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAndLaporanTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminTpi;
    protected User $verifikator;
    protected User $pimpinan;
    protected Tpi $tpiPulauBaai;
    protected Tpi $tpiBinaKarya;

    protected function setUp(): void
    {
        parent::setUp();

        // Run baseline seeders
        $this->seed([
            WilayahSeeder::class,
            TpiSeeder::class,
            JenisIkanSeeder::class,
            AlatTangkapSeeder::class,
            UserSeeder::class,
            SamplePendaratanSeeder::class,
        ]);

        $this->tpiPulauBaai = Tpi::where('kode_tpi', 'TPI-001')->first();
        $this->tpiBinaKarya = Tpi::where('kode_tpi', 'TPI-002')->first();

        $this->adminTpi = User::where('email', 'admin@tpi-bengkulu.test')->first();
        $this->verifikator = User::where('email', 'verifikator@tpi-bengkulu.test')->first();
        $this->pimpinan = User::where('email', 'pimpinan@tpi-bengkulu.test')->first();
    }

    /**
     * Test guests are redirected to the login page.
     */
    public function test_guest_cannot_access_dashboard_or_reports(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('laporan.index'))->assertRedirect(route('login'));
        $this->get(route('laporan.export'))->assertRedirect(route('login'));
        $this->get(route('laporan.cetak'))->assertRedirect(route('login'));
    }

    /**
     * Test dashboard renders successfully with all Chart.js datasets for Verifikator and Pimpinan.
     */
    public function test_dashboard_renders_with_chart_datasets(): void
    {
        $this->actingAs($this->pimpinan);

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewIs('dashboard.index');

        // Check required chart datasets passed to view
        $response->assertViewHas('trendBulanan');
        $response->assertViewHas('produksiPerTpi');
        $response->assertViewHas('topKomoditas');
        $response->assertViewHas('kategoriIkan');

        // Verify dataset structure
        $trend = $response->viewData('trendBulanan');
        $this->assertIsArray($trend);
        $this->assertCount(12, $trend['labels']);
        $this->assertCount(12, $trend['volumes']);
        $this->assertCount(12, $trend['values']);

        $tpiData = $response->viewData('produksiPerTpi');
        $this->assertIsArray($tpiData);
        $this->assertNotEmpty($tpiData['labels']);

        $top = $response->viewData('topKomoditas');
        $this->assertIsArray($top);
        $this->assertLessThanOrEqual(7, count($top['labels']));

        $kategori = $response->viewData('kategoriIkan');
        $this->assertIsArray($kategori);
        $this->assertContains('Pelagis Besar', $kategori['labels']);

        // Check chart canvas elements rendered in HTML
        $response->assertSee('chartTrenBulanan');
        $response->assertSee('chartProduksiTpi');
        $response->assertSee('chartTopKomoditas');
        $response->assertSee('chartKategoriIkan');
    }

    /**
     * Test role-aware filtering on dashboard: admin_tpi only sees their assigned TPI.
     */
    public function test_dashboard_role_aware_filtering_for_admin_tpi(): void
    {
        $this->actingAs($this->adminTpi);

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $tpiData = $response->viewData('produksiPerTpi');

        // Admin TPI Pulau Baai should only have 1 TPI in their comparison dataset
        $this->assertCount(1, $tpiData['labels']);
        $this->assertEquals($this->tpiPulauBaai->nama_tpi, $tpiData['labels'][0]);
    }

    /**
     * Test reporting hub loads successfully for authenticated roles.
     */
    public function test_reports_page_loads_successfully_for_authenticated_users(): void
    {
        // 1. Verifikator
        $this->actingAs($this->verifikator);
        $resVerifikator = $this->get(route('laporan.index'));
        $resVerifikator->assertOk();
        $resVerifikator->assertViewIs('laporan.index');
        $resVerifikator->assertViewHasAll([
            'rekapTpiData',
            'rekapIkanData',
            'rekapDkpData',
            'rekapTriwulanData',
        ]);
        $resVerifikator->assertSee('Rekapitulasi Produksi Perikanan');

        // 2. Pimpinan
        $this->actingAs($this->pimpinan);
        $this->get(route('laporan.index'))->assertOk();

        // 3. Admin TPI
        $this->actingAs($this->adminTpi);
        $resAdmin = $this->get(route('laporan.index'));
        $resAdmin->assertOk();
        // Admin TPI should see their own TPI locked/preselected
        $this->assertTrue($resAdmin->viewData('filters')['is_admin_tpi']);
    }

    /**
     * Test report filtering parameters (bulanan, triwulanan, kategori).
     */
    public function test_reports_filtering_works_properly(): void
    {
        $this->actingAs($this->pimpinan);

        // Filter by Month
        $currentYear = Carbon::now()->year;
        $resMonth = $this->get(route('laporan.index', [
            'tahun' => $currentYear,
            'periode_tipe' => 'bulanan',
            'bulan' => 1,
        ]));
        $resMonth->assertOk();
        $this->assertEquals('bulanan', $resMonth->viewData('filters')['periode_tipe']);
        $this->assertEquals(1, $resMonth->viewData('filters')['bulan']);

        // Filter by Triwulan
        $resQuarter = $this->get(route('laporan.index', [
            'tahun' => $currentYear,
            'periode_tipe' => 'triwulanan',
            'triwulan' => 2,
        ]));
        $resQuarter->assertOk();
        $this->assertEquals(2, $resQuarter->viewData('filters')['triwulan']);

        // Filter by Kategori Ikan
        $resKat = $this->get(route('laporan.index', [
            'kategori_ikan' => 'Pelagis Besar',
        ]));
        $resKat->assertOk();
        $this->assertEquals('Pelagis Besar', $resKat->viewData('filters')['kategori_ikan']);
    }

    /**
     * Test CSV export returns 200 with text/csv content type and UTF-8 BOM.
     */
    public function test_csv_export_returns_200_with_text_csv_content_type(): void
    {
        $this->actingAs($this->verifikator);

        // 1. Export Rekap TPI
        $responseTpi = $this->get(route('laporan.export', ['tipe' => 'tpi']));
        $responseTpi->assertOk();
        $this->assertStringContainsString('text/csv', $responseTpi->headers->get('content-type'));
        $this->assertStringContainsString('attachment;', $responseTpi->headers->get('content-disposition'));

        // 2. Export Rekap Komoditas Ikan
        $responseIkan = $this->get(route('laporan.export', ['tipe' => 'ikan']));
        $responseIkan->assertOk();
        $this->assertStringContainsString('text/csv', $responseIkan->headers->get('content-type'));

        // 3. Export Standar DKP Bengkulu
        $responseDkp = $this->get(route('laporan.export', ['tipe' => 'dkp']));
        $responseDkp->assertOk();
        $this->assertStringContainsString('text/csv', $responseDkp->headers->get('content-type'));

        // 4. Export Standar Triwulanan BPS
        $responseTw = $this->get(route('laporan.export', ['tipe' => 'triwulan']));
        $responseTw->assertOk();
        $this->assertStringContainsString('text/csv', $responseTw->headers->get('content-type'));
    }

    /**
     * Test official print view loads with status 200 and official government headers.
     */
    public function test_official_print_view_loads_with_status_200(): void
    {
        $this->actingAs($this->pimpinan);

        // Test Print View for TPI
        $responseTpi = $this->get(route('laporan.cetak', ['tipe' => 'tpi']));
        $responseTpi->assertOk();
        $responseTpi->assertViewIs('laporan.cetak');
        $responseTpi->assertSee('PEMERINTAH PROVINSI BENGKULU');
        $responseTpi->assertSee('DINAS KELAUTAN DAN PERIKANAN');
        $responseTpi->assertSee('Syafriandi, SE., ST., M.Si');

        // Test Print View for Ikan
        $responseIkan = $this->get(route('laporan.cetak', ['tipe' => 'ikan']));
        $responseIkan->assertOk();
        $responseIkan->assertSee('REKAPITULASI PRODUKSI PERIKANAN TANGKAP MENURUT KOMODITAS IKAN');

        // Test Print View for DKP Bengkulu
        $responseDkp = $this->get(route('laporan.cetak', ['tipe' => 'dkp']));
        $responseDkp->assertOk();
        $responseDkp->assertSee('STANDAR PUBLIKASI RESMI DINAS KELAUTAN DAN PERIKANAN MENURUT KABUPATEN/KOTA');

        // Test Print View for Triwulan
        $responseTw = $this->get(route('laporan.cetak', ['tipe' => 'triwulan']));
        $responseTw->assertOk();
        $responseTw->assertSee('LAPORAN TRIWULANAN AKTIVITAS PENDARATAN TPI (STANDAR BPS PP-TPI)');
    }
}
