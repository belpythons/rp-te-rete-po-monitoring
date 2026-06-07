<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Procurement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcurementWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::create([
            'name' => 'Admin KMI',
            'email' => 'admin@outlook.com',
            'password' => bcrypt('adminkmi123'),
        ]);
    }

    public function test_dashboard_redirects_unauthenticated_user(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_admin_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_create_procurement_validation_strictness(): void
    {
        // 1. Submit empty should fail
        $response = $this->actingAs($this->admin)->post('/procurement/store', [
            'status' => 'RP',
        ]);
        $response->assertSessionHasErrors(['kode_pengadaan', 'nama_barang']);

        // 2. Submit valid RP
        $response = $this->actingAs($this->admin)->post('/procurement/store', [
            'status' => 'RP',
            'kode_pengadaan' => 'PRQ-TEST-001',
            'nama_barang' => 'Test Laptop',
            'quantity' => '10 Pcs',
            'departemen' => 'IT',
            'tanggal' => '2026-06-01',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('procurements', [
            'kode_pengadaan' => 'PRQ-TEST-001',
            'nama_barang' => 'Test Laptop',
            'status' => 'RP',
            'tanggal_in' => '2026-06-01 00:00:00',
        ]);
    }

    public function test_workflow_phase_transitions(): void
    {
        // Setup initial RP
        $procurement = Procurement::create([
            'status' => 'RP',
            'kode_pengadaan' => 'PRQ-TEST-002',
            'nama_barang' => 'Test Server',
            'tanggal_in' => '2026-06-01',
        ]);

        // Transition 1: RP -> TE
        $response = $this->actingAs($this->admin)->post("/procurement/approve-phase/{$procurement->id}");
        $response->assertRedirect();
        
        $procurement->refresh();
        $this->assertEquals('TE', $procurement->status);
        $this->assertNotNull($procurement->tanggal_te);
        $this->assertNull($procurement->tanggal_out);

        // Transition 2: TE -> RE-TE
        $response = $this->actingAs($this->admin)->post("/procurement/approve-phase/{$procurement->id}", [
            'target' => 'RE-TE',
        ]);
        $response->assertRedirect();
        
        $procurement->refresh();
        $this->assertEquals('RE-TE', $procurement->status);
        $this->assertNotNull($procurement->tanggal_rete);
        $this->assertNull($procurement->tanggal_out);
        $oldReteDate = $procurement->tanggal_rete;

        // Transition 3: RE-TE -> PO
        // Let's sleep a moment or travel in time to check preserve
        $response = $this->actingAs($this->admin)->post("/procurement/approve-phase/{$procurement->id}");
        $response->assertRedirect();
        
        $procurement->refresh();
        $this->assertEquals('PO', $procurement->status);
        $this->assertNotNull($procurement->tanggal_po);
        $this->assertNotNull($procurement->tanggal_out);
        // Original RETE date must remain preserved, NOT updated to PO transition time!
        $this->assertEquals($oldReteDate, $procurement->tanggal_rete);
    }

    public function test_workflow_po_transition_from_te_directly(): void
    {
        // Setup initial TE
        $procurement = Procurement::create([
            'status' => 'TE',
            'kode_pengadaan' => 'PRQ-TEST-003',
            'nama_barang' => 'Test Cloud',
            'tanggal_in' => '2026-06-01',
            'tanggal_te' => '2026-06-02',
        ]);

        // Transition: TE -> PO directly (RE-TE remains null)
        $response = $this->actingAs($this->admin)->post("/procurement/approve-phase/{$procurement->id}", [
            'target' => 'PO',
        ]);
        $response->assertRedirect();
        
        $procurement->refresh();
        $this->assertEquals('PO', $procurement->status);
        $this->assertNotNull($procurement->tanggal_po);
        $this->assertNotNull($procurement->tanggal_out);
        // RETE must remain null, not updated to now()
        $this->assertNull($procurement->tanggal_rete);
    }
}
