<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Procurement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

class ProcurementBddTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Disable physical file check for Inertia views in test environment
        config(['inertia.testing.ensure_pages_exist' => false]);
    }

    /**
     * SKENARIO 1: Perilaku Dashboard Utama (Overview)
     *
     * Scenario: User melihat rekapitulasi keseluruhan di Dashboard tanpa filter fase.
     * Given sistem memiliki berbagai data pengadaan dengan fase berbeda (RP, TE, RE-TE, PO) dan tanggal_masuk yang bervariasi.
     * And user admin telah login.
     * When user mengakses rute `/dashboard`.
     * Then sistem mengembalikan status 200 OK.
     * And sistem merender komponen Inertia `Dashboard/Index`.
     * And response Inertia memuat data untuk grafik (Line Chart) dan seluruh stats card.
     * And data tabel dipaginasi sebanyak 15 item.
     */
    public function test_dashboard_overview_behavior()
    {
        // Given
        Procurement::factory()->create(['phase' => 'RP', 'tanggal_masuk' => '2026-05-10']);
        Procurement::factory()->create(['phase' => 'TE', 'tanggal_masuk' => '2026-05-12']);
        Procurement::factory()->create(['phase' => 'RE-TE', 'tanggal_masuk' => '2026-06-01']);
        Procurement::factory()->create(['phase' => 'PO', 'tanggal_masuk' => '2026-06-05']);

        // And
        $admin = User::factory()->create();

        // When
        $response = $this->actingAs($admin)->get('/dashboard');

        // Then
        $response->assertStatus(200);

        // And
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/Index')
            ->has('chartData')
            ->has('chartData.categories')
            ->has('chartData.series')
            ->where('totalRP', 1)
            ->where('totalTE', 1)
            ->where('totalRETE', 1)
            ->where('totalPO', 1)
            ->has('procurements.data')
            ->has('procurements.links')
        );
    }

    /**
     * SKENARIO 2: Isolasi Data pada Halaman Spesifik (Contoh: Request Purchasing)
     *
     * Scenario: User mengakses halaman spesifik fase dan hanya melihat data yang relevan.
     * Given sistem memiliki 5 data dengan fase 'RP' dan 3 data dengan fase 'TE'.
     * And user admin telah login.
     * When user mengakses rute `/request-purchasing`.
     * Then sistem merender komponen Inertia `RequestPurchasing/Index`.
     * And response Inertia data tabel HANYA berisi 5 data 'RP'.
     * And data 'TE' tidak ikut ter-load ke frontend.
     * And hanya 1 bubble/card rekapitulasi ("Total RP") yang dikirimkan oleh backend.
     */
    public function test_specific_phase_data_isolation()
    {
        // Given
        Procurement::factory()->count(5)->create(['phase' => 'RP']);
        Procurement::factory()->count(3)->create(['phase' => 'TE']);

        // And
        $admin = User::factory()->create();

        // When
        $response = $this->actingAs($admin)->get('/request-purchasing');

        // Then
        $response->assertInertia(fn (Assert $page) => $page
            ->component('RequestPurchasing/Index')
            ->where('totalRP', 5)
            ->has('procurements.data', 5)
            ->where('procurements.data.0.phase', 'RP')
            ->where('procurements.data.1.phase', 'RP')
            ->where('procurements.data.2.phase', 'RP')
            ->where('procurements.data.3.phase', 'RP')
            ->where('procurements.data.4.phase', 'RP')
            ->missing('totalTE')
            ->missing('totalRETE')
            ->missing('totalPO')
        );
    }

    /**
     * SKENARIO 4: Validasi Status Pengadaan
     *
     * Scenario: Sistem menolak status di luar ketentuan yang berlaku.
     * Given user admin telah login.
     * When user mencoba membuat atau memperbarui pengadaan dengan status "Draft" atau "Lolos".
     * Then sistem mengembalikan error validasi (Session/Inertia errors) pada field `status`.
     * When user memperbarui pengadaan dengan status "Disetujui".
     * Then sistem berhasil menyimpan pembaruan ke database.
     */
    public function test_procurement_status_validation()
    {
        // Given
        $admin = User::factory()->create();
        $procurement = Procurement::factory()->create(['status' => 'Pending', 'phase' => 'RP']);

        // When (Create with Draft)
        $responseCreate = $this->actingAs($admin)->post('/procurement/store', [
            'no' => '999',
            'rp_number' => 'RP-99999',
            'description' => 'Test Item',
            'date_created' => '15 June 2026',
            'status' => 'Draft',
            'phase' => 'RP'
        ]);

        // Then
        $responseCreate->assertSessionHasErrors('status');

        // When (Update with Lolos)
        $responseUpdate = $this->actingAs($admin)->put("/procurement/update/{$procurement->id}", [
            'no' => $procurement->no,
            'rp_number' => $procurement->rp_number,
            'description' => 'Updated Description',
            'date_created' => $procurement->date_created,
            'status' => 'Lolos',
            'phase' => 'RP'
        ]);

        // Then
        $responseUpdate->assertSessionHasErrors('status');

        // When (Update with Disetujui)
        $responseSuccess = $this->actingAs($admin)->put("/procurement/update/{$procurement->id}", [
            'no' => $procurement->no,
            'rp_number' => $procurement->rp_number,
            'description' => 'Updated Description 2',
            'date_created' => $procurement->date_created,
            'status' => 'Disetujui',
            'phase' => 'TE'
        ]);

        // Then
        $responseSuccess->assertSessionHasNoErrors();
        $this->assertDatabaseHas('procurements', [
            'id' => $procurement->id,
            'status' => 'Disetujui',
            'phase' => 'TE',
            'description' => 'Updated Description 2'
        ]);
    }
}
