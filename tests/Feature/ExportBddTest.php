<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Procurement;
use App\Exports\ProcurementExport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;

class ExportBddTest extends TestCase
{
    use RefreshDatabase;

    /**
     * SKENARIO 3: Sinkronisasi Filter Ekspor Data
     *
     * Scenario: User mengekspor data Excel dari halaman spesifik dengan filter bulan.
     * Given sistem memiliki data 'TE' pada bulan Mei 2026 dan bulan Juni 2026, serta data 'PO' di bulan Mei 2026.
     * And user admin telah login.
     * When user mengirim request ke rute export Excel (misal `/report/export/excel`) dengan query parameter `phase=TE` dan `month_year=2026-05`.
     * Then sistem mengunduh file Excel.
     * And isi file Excel tersebut HANYA mengandung data 'TE' pada bulan Mei 2026.
     */
    public function test_export_excel_filters_sync()
    {
        // Given
        // TE in May 2026
        Procurement::factory()->create([
            'phase' => 'TE',
            'tanggal_masuk' => '2026-05-15',
            'description' => 'Target TE Mei'
        ]);

        // TE in June 2026
        Procurement::factory()->create([
            'phase' => 'TE',
            'tanggal_masuk' => '2026-06-10',
            'description' => 'Target TE Juni'
        ]);

        // PO in May 2026
        Procurement::factory()->create([
            'phase' => 'PO',
            'tanggal_masuk' => '2026-05-20',
            'description' => 'Target PO Mei'
        ]);

        // And
        $admin = User::factory()->create();

        // Fake Excel exports
        Excel::fake();

        // Freeze time to make filename predictable (e.g., 14:00:00)
        $this->travelTo(now()->setTime(14, 0, 0));
        $expectedFilename = 'laporan_procurement_te_2026-05_140000.xlsx';

        // When
        $response = $this->actingAs($admin)->get('/report/export/excel?phase=TE&month_year=2026-05');

        // Then
        $response->assertStatus(200);

        // And
        Excel::assertDownloaded(
            $expectedFilename,
            function (ProcurementExport $export) {
                // Verify the collection only has the specific filtered item
                $collection = $export->collection();
                return $collection->count() === 1 &&
                       $collection->first()->phase === 'TE' &&
                       $collection->first()->description === 'Target TE Mei';
            }
        );
    }
}
