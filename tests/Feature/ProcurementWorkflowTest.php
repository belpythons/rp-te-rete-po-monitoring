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
        $response->assertSessionHasErrors(['no', 'rp_number', 'description', 'date_created']);

        // 2. Submit valid RP
        $response = $this->actingAs($this->admin)->post('/procurement/store', [
            'status' => 'RP',
            'no' => '49',
            'rp_number' => 'PRQ-TEST-001',
            'description' => 'Test Laptop',
            'date_created' => 'Monday, June 1, 2026',
            'quantity' => '10 Pcs',
            'departemen' => 'IT',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('procurements', [
            'no' => '49',
            'rp_number' => 'PRQ-TEST-001',
            'description' => 'Test Laptop',
            'status' => 'RP',
            'date_created' => 'Monday, June 1, 2026',
        ]);
    }

    public function test_workflow_phase_transitions(): void
    {
        // Setup initial RP
        $procurement = Procurement::create([
            'no' => '50',
            'status' => 'RP',
            'rp_number' => 'PRQ-TEST-002',
            'description' => 'Test Server',
            'date_created' => 'Monday, June 1, 2026',
        ]);

        // Transition 1: RP -> TE
        $response = $this->actingAs($this->admin)->post("/procurement/approve-phase/{$procurement->id}");
        $response->assertRedirect();
        
        $procurement->refresh();
        $this->assertEquals('TE', $procurement->status);
        $this->assertNotNull($procurement->te_in);

        // Transition 2: TE -> RE-TE
        $response = $this->actingAs($this->admin)->post("/procurement/approve-phase/{$procurement->id}", [
            'target' => 'RE-TE',
        ]);
        $response->assertRedirect();
        
        $procurement->refresh();
        $this->assertEquals('RE-TE', $procurement->status);
        $this->assertNotNull($procurement->re_te);
        $oldReteDate = $procurement->re_te;

        // Transition 3: RE-TE -> PO
        $response = $this->actingAs($this->admin)->post("/procurement/approve-phase/{$procurement->id}");
        $response->assertRedirect();
        
        $procurement->refresh();
        $this->assertEquals('PO', $procurement->status);
        $this->assertNotNull($procurement->po);
        $this->assertEquals($oldReteDate, $procurement->re_te);
    }

    public function test_workflow_po_transition_from_te_directly(): void
    {
        // Setup initial TE
        $procurement = Procurement::create([
            'no' => '51',
            'status' => 'TE',
            'rp_number' => 'PRQ-TEST-003',
            'description' => 'Test Cloud',
            'date_created' => 'Monday, June 1, 2026',
            'te_in' => 'Tuesday, June 2, 2026',
        ]);

        // Transition: TE -> PO directly (RE-TE remains null)
        $response = $this->actingAs($this->admin)->post("/procurement/approve-phase/{$procurement->id}", [
            'target' => 'PO',
        ]);
        $response->assertRedirect();
        
        $procurement->refresh();
        $this->assertEquals('PO', $procurement->status);
        $this->assertNotNull($procurement->po);
        $this->assertNull($procurement->re_te);
    }
}
