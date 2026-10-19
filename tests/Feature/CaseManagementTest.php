<?php

namespace Tests\Feature;

use App\Models\CaseCommunication;
use App\Models\CaseTask;
use App\Models\Category;
use App\Models\Company;
use App\Models\Enquiry;
use App\Models\Opportunity;
use App\Models\User;
use App\Services\CaseManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CaseManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test unauthenticated guests are redirected to login from staff cases pipeline.
     */
    public function test_guests_are_redirected_to_login_from_staff_cases_index(): void
    {
        $response = $this->get('/admin/cases');

        $response->assertRedirect('/login');
    }

    /**
     * Test regular customers receive 403 Forbidden when attempting to access staff cases.
     */
    public function test_customers_receive_403_forbidden_from_staff_cases(): void
    {
        $customer = User::factory()->customer()->create();

        $response = $this->actingAs($customer)->get('/admin/cases');

        $response->assertStatus(403);
    }

    /**
     * Test case managers and admins can view the case management pipeline.
     */
    public function test_case_managers_and_admins_can_view_cases_pipeline(): void
    {
        $caseManager = User::factory()->caseManager()->create();

        $enquiry = Enquiry::create([
            'reference_code' => 'FD-CASE-202610-1001',
            'full_name' => 'Kavita Krishnan',
            'phone' => '9876500000',
            'email' => 'kavita@example.com',
            'target_state' => 'KA',
            'target_city' => 'Bengaluru',
            'budget_range' => '15l-30l',
            'preferred_timeline' => '1-3-months',
            'commercial_space_status' => 'searching',
            'business_experience' => 'first_time',
            'consent_given' => true,
            'consent_recorded_at' => now(),
            'status' => 'new',
            'stage' => 'new',
        ]);

        $response = $this->actingAs($caseManager)->get('/admin/cases');

        $response->assertStatus(200);
        $response->assertSee('FD-CASE-202610-1001');
        $response->assertSee('Kavita Krishnan');
        $response->assertSee('Managed Enquiries &amp; Applications Pipeline', false);
    }

    /**
     * Test case manager can view the case workspace with dossiers.
     */
    public function test_case_managers_can_view_case_workspace(): void
    {
        $caseManager = User::factory()->caseManager()->create();

        $category = Category::factory()->create();
        $company = Company::factory()->create(['brand_name' => 'Dr Lal PathLabs']);
        $opp = Opportunity::factory()->published()->create([
            'category_id' => $category->id,
            'company_id' => $company->id,
            'title' => 'Dr Lal Collection Centre',
        ]);

        $enquiry = Enquiry::create([
            'reference_code' => 'FD-CASE-202610-1002',
            'opportunity_id' => $opp->id,
            'full_name' => 'Amitabh Sen',
            'phone' => '9876511111',
            'email' => 'amitabh@example.com',
            'target_state' => 'WB',
            'target_city' => 'Kolkata',
            'budget_range' => '5l-15l',
            'preferred_timeline' => 'immediate',
            'commercial_space_status' => 'owned',
            'carpet_area_sqft' => 250,
            'business_experience' => 'existing_retail',
            'consent_given' => true,
            'consent_recorded_at' => now(),
            'ip_address' => '127.0.0.1',
            'status' => 'new',
            'stage' => 'new',
        ]);

        $response = $this->actingAs($caseManager)->get("/admin/cases/{$enquiry->id}");

        $response->assertStatus(200);
        $response->assertSee('FD-CASE-202610-1002');
        $response->assertSee('Amitabh Sen');
        $response->assertSee('Dr Lal PathLabs');
        $response->assertSee('Applicant Profile &amp; Capacity Dossier', false);
    }

    /**
     * Test assigning a case manager updates assignee and records status history.
     */
    public function test_case_assignment_updates_assignee_and_seeds_checklist(): void
    {
        $admin = User::factory()->admin()->create();
        $specialist = User::factory()->caseManager()->create(['name' => 'Priya Sharma']);

        $enquiry = Enquiry::create([
            'reference_code' => 'FD-CASE-202610-1003',
            'full_name' => 'Rohan Mehra',
            'phone' => '9876522222',
            'email' => 'rohan@example.com',
            'target_state' => 'MH',
            'target_city' => 'Mumbai',
            'budget_range' => '15l-30l',
            'status' => 'new',
            'stage' => 'new',
            'consent_given' => true,
            'consent_recorded_at' => now(),
        ]);

        $this->assertNull($enquiry->assigned_to_user_id);
        $this->assertCount(0, $enquiry->tasks);

        $response = $this->actingAs($admin)->post("/admin/cases/{$enquiry->id}/assign", [
            'assigned_to_user_id' => $specialist->id,
            'comment' => 'Assigning senior specialist for Tier 1 Mumbai retail inquiry.',
        ]);

        $response->assertRedirect();
        $enquiry->refresh();

        $this->assertEquals($specialist->id, $enquiry->assigned_to_user_id);
        $this->assertEquals('intake_assessment', $enquiry->stage);
        $this->assertNotNull($enquiry->assigned_at);

        // Verify status history recorded
        $this->assertDatabaseHas('case_status_histories', [
            'enquiry_id' => $enquiry->id,
            'user_id' => $admin->id,
            'to_status' => 'Assigned to Priya Sharma',
        ]);

        // Verify standard checklist tasks were automatically seeded
        $this->assertGreaterThan(0, $enquiry->tasks()->count());
    }

    /**
     * Test case stage transition updates stage and logs audit trail.
     */
    public function test_case_stage_transition_updates_stage_and_audit_history(): void
    {
        $caseManager = User::factory()->caseManager()->create();

        $enquiry = Enquiry::create([
            'reference_code' => 'FD-CASE-202610-1004',
            'full_name' => 'Sunita Rao',
            'phone' => '9876533333',
            'email' => 'sunita@example.com',
            'target_state' => 'DL',
            'target_city' => 'Delhi',
            'budget_range' => '30l-plus',
            'status' => 'new',
            'stage' => 'intake_assessment',
            'consent_given' => true,
            'consent_recorded_at' => now(),
        ]);

        $response = $this->actingAs($caseManager)->post("/admin/cases/{$enquiry->id}/status", [
            'stage' => 'submitted_to_brand',
            'comment' => 'Consent package verified and dossier dispatched to brand expansion desk.',
        ]);

        $response->assertRedirect();
        $enquiry->refresh();

        $this->assertEquals('submitted_to_brand', $enquiry->stage);

        $this->assertDatabaseHas('case_status_histories', [
            'enquiry_id' => $enquiry->id,
            'user_id' => $caseManager->id,
            'to_status' => 'Application Delivered to Brand',
            'comment' => 'Consent package verified and dossier dispatched to brand expansion desk.',
        ]);
    }

    /**
     * Test adding and toggling checklist tasks.
     */
    public function test_creating_and_toggling_checklist_tasks(): void
    {
        $caseManager = User::factory()->caseManager()->create();

        $enquiry = Enquiry::create([
            'reference_code' => 'FD-CASE-202610-1005',
            'full_name' => 'Anil Kapoor',
            'phone' => '9876544444',
            'email' => 'anil@example.com',
            'target_state' => 'UP',
            'target_city' => 'Lucknow',
            'budget_range' => '5l-15l',
            'consent_given' => true,
            'consent_recorded_at' => now(),
        ]);

        // Add task
        $response = $this->actingAs($caseManager)->post("/admin/cases/{$enquiry->id}/tasks", [
            'title' => 'Confirm electricity load capacity of property',
            'due_date' => now()->addDays(3)->toDateString(),
        ]);

        $response->assertRedirect();

        $task = CaseTask::where('enquiry_id', $enquiry->id)->first();
        $this->assertNotNull($task);
        $this->assertEquals('Confirm electricity load capacity of property', $task->title);
        $this->assertFalse($task->is_completed);

        // Toggle task to completed
        $toggleResponse = $this->actingAs($caseManager)->patch("/admin/cases/tasks/{$task->id}/toggle");
        $toggleResponse->assertRedirect();

        $task->refresh();
        $this->assertTrue($task->is_completed);
        $this->assertEquals($caseManager->id, $task->completed_by_user_id);

        // Toggle back to incomplete
        $this->actingAs($caseManager)->patch("/admin/cases/tasks/{$task->id}/toggle");
        $task->refresh();
        $this->assertFalse($task->is_completed);
    }

    /**
     * Non-negotiable Security & Privacy Test:
     * Customer can view customer-visible milestone updates, but CANNOT view confidential internal notes.
     */
    public function test_strict_customer_visibility_boundary(): void
    {
        $customer = User::factory()->customer()->create(['name' => 'Deepak Patel', 'email' => 'deepak@example.com']);
        $caseManager = User::factory()->caseManager()->create(['name' => 'Anjali Mehta']);

        $enquiry = Enquiry::create([
            'reference_code' => 'FD-CASE-202610-9999',
            'user_id' => $customer->id,
            'assigned_to_user_id' => $caseManager->id,
            'full_name' => $customer->name,
            'phone' => '9876555555',
            'email' => $customer->email,
            'target_state' => 'GJ',
            'target_city' => 'Ahmedabad',
            'budget_range' => '15l-30l',
            'status' => 'shortlisted',
            'stage' => 'intake_assessment',
            'consent_given' => true,
            'consent_recorded_at' => now(),
        ]);

        $caseService = app(CaseManagementService::class);

        // 1. Staff logs confidential internal note
        $caseService->logCommunication($enquiry, [
            'type' => CaseCommunication::TYPE_INTERNAL_NOTE,
            'subject' => 'Confidential Credit & Net Worth Risk Assessment',
            'message' => 'INTERNAL ONLY: Applicant credit score is acceptable but working capital margin is tight. Proceed with caution.',
        ], $caseManager);

        // 2. Staff logs customer-visible milestone update
        $caseService->logCommunication($enquiry, [
            'type' => CaseCommunication::TYPE_CUSTOMER_UPDATE,
            'subject' => 'Brand Review Initiated',
            'message' => 'Good news! Your preliminary profile has been forwarded to the franchisor expansion team for territory review.',
        ], $caseManager);

        // Customer accesses their dashboard
        $customerResponse = $this->actingAs($customer)->get('/dashboard');

        $customerResponse->assertStatus(200);

        // 1. Customer CAN see the public milestone update
        $customerResponse->assertSee('Brand Review Initiated');
        $customerResponse->assertSee('Good news! Your preliminary profile has been forwarded');

        // 2. Customer CANNOT see the confidential internal note (Strict Boundary Test)
        $customerResponse->assertDontSee('Confidential Credit & Net Worth Risk Assessment');
        $customerResponse->assertDontSee('INTERNAL ONLY: Applicant credit score is acceptable');

        // Staff accesses the case workspace
        $staffResponse = $this->actingAs($caseManager)->get("/admin/cases/{$enquiry->id}");

        $staffResponse->assertStatus(200);
        // Staff CAN see both
        $staffResponse->assertSee('Confidential Credit & Net Worth Risk Assessment');
        $staffResponse->assertSee('Brand Review Initiated');
    }

    /**
     * Test case priority and next follow-up date scheduling.
     */
    public function test_case_priority_and_followup_update(): void
    {
        $caseManager = User::factory()->caseManager()->create();

        $enquiry = Enquiry::create([
            'reference_code' => 'FD-CASE-202610-1006',
            'full_name' => 'Meera Nair',
            'phone' => '9876566666',
            'email' => 'meera@example.com',
            'target_state' => 'KL',
            'target_city' => 'Kochi',
            'budget_range' => '5l-15l',
            'priority' => 'normal',
            'consent_given' => true,
            'consent_recorded_at' => now(),
        ]);

        $followUp = now()->addDays(5)->toDateString();

        $response = $this->actingAs($caseManager)->post("/admin/cases/{$enquiry->id}/followup", [
            'priority' => 'urgent',
            'next_follow_up_at' => $followUp,
        ]);

        $response->assertRedirect();
        $enquiry->refresh();

        $this->assertEquals('urgent', $enquiry->priority);
        $this->assertEquals($followUp, $enquiry->next_follow_up_at->toDateString());
    }
}
