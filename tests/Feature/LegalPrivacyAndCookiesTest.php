<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use App\Models\Document;
use App\Models\DocumentConsent;
use App\Models\Enquiry;
use App\Models\Grievance;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LegalPrivacyAndCookiesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test public statutory policies render with expected DPDPA and IT Rules disclosures.
     */
    public function test_statutory_policies_and_cookie_banner_render(): void
    {
        // Privacy Policy
        $privacyResponse = $this->get('/privacy-policy');
        $privacyResponse->assertStatus(200);
        $privacyResponse->assertSee('Digital Personal Data Protection Act');
        $privacyResponse->assertSee('Data Fiduciary');
        $privacyResponse->assertSee('Chief Compliance &amp; Grievance Officer', false);
        $privacyResponse->assertSee('fd-cookie-banner'); // Cookie banner layout check

        // Terms of Service
        $termsResponse = $this->get('/terms-of-service');
        $termsResponse->assertStatus(200);
        $termsResponse->assertSee('Non-Negotiable Regulatory &amp; Commercial Disclaimer', false);
        $termsResponse->assertSee('guarantees business profitability', false);

        // Cookie Policy
        $cookieResponse = $this->get('/cookie-policy');
        $cookieResponse->assertStatus(200);
        $cookieResponse->assertSee('Cookie Policy');
        $cookieResponse->assertSee('Zero Unsolicited Tracking');
        $cookieResponse->assertSee('laravel_session');
        $cookieResponse->assertSee('XSRF-TOKEN');
        $cookieResponse->assertSee('fd_cookie_consent');
    }

    /**
     * Test public can submit a formal grievance / fraud report.
     */
    public function test_public_can_submit_formal_grievance(): void
    {
        $category = Category::create(['name' => 'Automotive', 'slug' => 'automotive', 'is_active' => true]);
        $company = Company::create([
            'legal_name' => 'AutoBrand Pvt Ltd',
            'brand_name' => 'AutoBrand',
            'slug' => 'autobrand',
            'trade_name' => 'AutoBrand',
            'cin' => 'U12345DL2024PTC123456',
            'status' => 'verified',
        ]);
        $opportunity = Opportunity::create([
            'company_id' => $company->id,
            'category_id' => $category->id,
            'title' => 'Speedy Tyre Dealership',
            'slug' => 'speedy-tyre-dealership',
            'summary' => 'Auto tyres dealership',
            'opportunity_type' => 'dealership',
            'min_investment' => 1000000,
            'max_investment' => 2000000,
            'status' => 'verified',
            'published_at' => now(),
        ]);

        $payload = [
            'reporter_type' => 'entrepreneur',
            'full_name' => 'Vikram Sharma',
            'email' => 'vikram@example.com',
            'phone' => '+91 98765 43210',
            'opportunity_id' => $opportunity->id,
            'category' => 'fee_fraud',
            'subject' => 'Unauthorized Cash Processing Fee Demanded by Agent',
            'description' => 'An unauthorized broker posing as brand representative demanded 50,000 INR cash.',
            'evidence_summary' => 'Recorded phone conversation and WhatsApp payment request screenshots.',
            'declaration' => '1',
        ];

        $response = $this->post('/grievance-and-fraud', $payload);

        $response->assertRedirect('/grievance-and-fraud/confirmed');
        $response->assertSessionHas('grievance_tracking_code');

        $this->assertDatabaseHas('grievances', [
            'email' => 'vikram@example.com',
            'category' => 'fee_fraud',
            'priority' => 'urgent', // Fee fraud automatically triggers urgent priority
            'status' => Grievance::STATUS_RECEIVED,
            'opportunity_id' => $opportunity->id,
            'company_id' => $company->id,
        ]);

        $grievance = Grievance::where('email', 'vikram@example.com')->first();
        $this->assertStringStartsWith('FD-GRV-', $grievance->tracking_code);
    }

    /**
     * Test grievance filing validation prevents submission without statutory declaration.
     */
    public function test_grievance_submission_requires_statutory_declaration(): void
    {
        $payload = [
            'reporter_type' => 'consumer',
            'full_name' => 'Aarav Patel',
            'email' => 'aarav@example.com',
            'phone' => '9876543210',
            'category' => 'misleading_financials',
            'subject' => 'Fake Turnover Claim',
            'description' => 'Broker promised 80% monthly profit margin in writing.',
            // Missing declaration
        ];

        $response = $this->post('/grievance-and-fraud', $payload);
        $response->assertSessionHasErrors(['declaration']);
        $this->assertDatabaseMissing('grievances', ['email' => 'aarav@example.com']);
    }

    /**
     * Test public complaint tracking by tracking code and email address.
     */
    public function test_public_can_track_complaint_status(): void
    {
        $grievance = Grievance::create([
            'tracking_code' => 'FD-GRV-202610-TEST',
            'reporter_type' => 'entrepreneur',
            'full_name' => 'Meera Nair',
            'email' => 'meera@example.com',
            'phone' => '9876511111',
            'category' => 'unauthorized_impersonation',
            'subject' => 'Fake Franchise Agency Using Brand Name',
            'description' => 'Domain fakebrand.in is soliciting franchise fees without authorization.',
            'status' => Grievance::STATUS_UNDER_INVESTIGATION,
            'priority' => 'urgent',
        ]);

        // Search with matching code and email
        $response = $this->get('/grievance-and-fraud/track?tracking_code=FD-GRV-202610-TEST&email=meera@example.com');
        $response->assertStatus(200);
        $response->assertSee('FD-GRV-202610-TEST');
        $response->assertSee('Fake Franchise Agency Using Brand Name');
        $response->assertSee('Under Investigation');

        // Search with mismatching email
        $mismatchResponse = $this->get('/grievance-and-fraud/track?tracking_code=FD-GRV-202610-TEST&email=wrong@example.com');
        $mismatchResponse->assertStatus(200);
        $mismatchResponse->assertSee('No Record Located');
    }

    /**
     * Test access controls for admin grievance queue.
     */
    public function test_unauthorized_users_cannot_access_admin_grievance_queue(): void
    {
        // Unauthenticated guest
        $this->get('/admin/grievances')->assertRedirect('/login');

        // Customer role
        $customer = User::factory()->customer()->create();
        $this->actingAs($customer)->get('/admin/grievances')->assertForbidden();
    }

    /**
     * Test staff can manage, assign, and formally resolve a grievance.
     */
    public function test_staff_can_assign_and_resolve_grievance(): void
    {
        $caseManager = User::factory()->caseManager()->create(['name' => 'Pooja Iyer']);
        $grievance = Grievance::create([
            'tracking_code' => 'FD-GRV-202610-MANAGE',
            'reporter_type' => 'whistleblower',
            'full_name' => 'Rohan Sen',
            'email' => 'rohan@example.com',
            'phone' => '9876522222',
            'category' => 'fee_fraud',
            'subject' => 'Kickback demand by regional broker',
            'description' => 'Broker demanded unofficial cash commission of 1 Lakh INR.',
            'status' => Grievance::STATUS_RECEIVED,
            'priority' => 'urgent',
        ]);

        // View queue and detail
        $this->actingAs($caseManager)
            ->get('/admin/grievances')
            ->assertStatus(200)
            ->assertSee('FD-GRV-202610-MANAGE');

        $this->actingAs($caseManager)
            ->get("/admin/grievances/{$grievance->id}")
            ->assertStatus(200)
            ->assertSee('Kickback demand by regional broker');

        // Assign to case manager
        $this->actingAs($caseManager)
            ->post("/admin/grievances/{$grievance->id}/assign", [
                'assigned_to_user_id' => $caseManager->id,
            ])
            ->assertRedirect();

        $grievance->refresh();
        $this->assertEquals($caseManager->id, $grievance->assigned_to_user_id);
        $this->assertEquals(Grievance::STATUS_UNDER_INVESTIGATION, $grievance->status);

        // Formally resolve with findings
        $this->actingAs($caseManager)
            ->post("/admin/grievances/{$grievance->id}/resolve", [
                'outcome' => Grievance::STATUS_RESOLVED,
                'resolution_notes' => 'Investigation verified unauthorized broker conduct. Notice served to regional broker; platform banned broker phone and reported to cybercell.',
            ])
            ->assertRedirect();

        $grievance->refresh();
        $this->assertEquals(Grievance::STATUS_RESOLVED, $grievance->status);
        $this->assertNotNull($grievance->resolved_at);
        $this->assertStringContainsString('banned broker', $grievance->resolution_notes);
    }

    /**
     * Test customer can download machine-readable personal data export (DPDPA 2023).
     */
    public function test_customer_can_download_personal_data_export(): void
    {
        $customer = User::factory()->customer()->create([
            'name' => 'Ananya Deshmukh',
            'email' => 'ananya@example.com',
            'phone' => '9876533333',
            'city' => 'Mumbai',
            'state' => 'MH',
        ]);

        $enquiry = Enquiry::create([
            'reference_code' => 'FD-CASE-202610-DPDP1',
            'user_id' => $customer->id,
            'full_name' => $customer->name,
            'phone' => $customer->phone,
            'email' => $customer->email,
            'target_state' => 'MH',
            'target_city' => 'Mumbai',
            'budget_range' => '15l-30l',
            'stage' => 'intake',
            'status' => 'new',
            'consent_given' => true,
            'consent_recorded_at' => now(),
        ]);

        // Grievance filed by this customer
        Grievance::create([
            'tracking_code' => 'FD-GRV-202610-ANAN',
            'reporter_type' => 'entrepreneur',
            'full_name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'category' => 'service_complaint',
            'subject' => 'Verification timeline delay inquiry',
            'description' => 'Inquiring regarding status update.',
            'status' => Grievance::STATUS_RESOLVED,
        ]);

        $response = $this->actingAs($customer)->get('/customer/data-export');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/json');

        $content = $response->streamedContent();
        $data = json_decode($content, true);

        $this->assertArrayHasKey('data_subject_export_metadata', $data);
        $this->assertArrayHasKey('principal_profile', $data);
        $this->assertEquals('Ananya Deshmukh', $data['principal_profile']['name']);
        $this->assertEquals('ananya@example.com', $data['principal_profile']['email']);

        $this->assertArrayHasKey('commercial_enquiries', $data);
        $this->assertCount(1, $data['commercial_enquiries']);
        $this->assertEquals('FD-CASE-202610-DPDP1', $data['commercial_enquiries'][0]['reference_code']);

        $this->assertArrayHasKey('statutory_grievances', $data);
        $this->assertCount(1, $data['statutory_grievances']);
        $this->assertEquals('FD-GRV-202610-ANAN', $data['statutory_grievances'][0]['tracking_code']);
    }

    /**
     * Test customer can permanently erase account and anonymize personal records under DPDPA 2023.
     */
    public function test_customer_can_permanently_erase_account_with_password(): void
    {
        $customer = User::factory()->customer()->create([
            'name' => 'Karan Malhotra',
            'email' => 'karan@example.com',
            'phone' => '9876544444',
            'password' => Hash::make('SecretPass123!'),
        ]);

        $enquiry = Enquiry::create([
            'reference_code' => 'FD-CASE-202610-ERAS1',
            'user_id' => $customer->id,
            'full_name' => $customer->name,
            'phone' => $customer->phone,
            'email' => $customer->email,
            'target_state' => 'DL',
            'target_city' => 'New Delhi',
            'budget_range' => '30l-50l',
            'consent_given' => true,
            'consent_recorded_at' => now(),
            'notes' => 'Original private notes from Karan',
        ]);

        // Attempt deletion with wrong password
        $failResponse = $this->actingAs($customer)->post('/customer/data-deletion', [
            'current_password' => 'WrongPassword!',
            'confirmation' => '1',
        ]);
        $failResponse->assertSessionHasErrors(['current_password']);
        $this->assertDatabaseHas('users', ['id' => $customer->id, 'email' => 'karan@example.com']);

        // Attempt deletion with correct password
        $successResponse = $this->actingAs($customer)->post('/customer/data-deletion', [
            'current_password' => 'SecretPass123!',
            'confirmation' => '1',
        ]);

        $successResponse->assertRedirect('/');
        $this->assertGuest(); // User session must be invalidated

        // Verify user account anonymization
        $customer->refresh();
        $this->assertStringStartsWith('Anonymized User #', $customer->name);
        $this->assertStringContainsString('@anonymized.franchisedekho.local', $customer->email);
        $this->assertNull($customer->phone);
        $this->assertFalse($customer->is_active);

        // Verify linked enquiry anonymization
        $enquiry->refresh();
        $this->assertEquals('Anonymized Data Principal', $enquiry->full_name);
        $this->assertEquals('+910000000000', $enquiry->phone);
        $this->assertStringContainsString('@anonymized.franchisedekho.local', $enquiry->email);
        $this->assertStringContainsString('REDACTED PURSUANT TO DATA PRINCIPAL ERASURE', $enquiry->notes);
    }

    /**
     * Test automated data retention scrubbing command scrubs unlinked expired guest applications.
     */
    public function test_artisan_scrub_expired_privacy_data_command(): void
    {
        // 1. Expired guest enquiry (older than 180 days, no user_id)
        $expiredGuestEnquiry = Enquiry::create([
            'reference_code' => 'FD-CASE-202601-OLD1',
            'user_id' => null,
            'full_name' => 'Old Guest Applicant',
            'phone' => '9876599999',
            'email' => 'oldguest@example.com',
            'target_state' => 'UP',
            'target_city' => 'Noida',
            'budget_range' => '5l-15l',
            'consent_given' => true,
            'consent_recorded_at' => Carbon::now()->subDays(200),
            'notes' => 'Old guest commercial notes',
            'ip_address' => '192.168.1.1',
        ]);
        // Manually update created_at to 200 days ago
        $expiredGuestEnquiry->created_at = Carbon::now()->subDays(200);
        $expiredGuestEnquiry->save();

        // 2. Recent guest enquiry (only 30 days old)
        $recentGuestEnquiry = Enquiry::create([
            'reference_code' => 'FD-CASE-202609-NEW1',
            'user_id' => null,
            'full_name' => 'Recent Guest Applicant',
            'phone' => '9876588888',
            'email' => 'recentguest@example.com',
            'target_state' => 'UP',
            'target_city' => 'Lucknow',
            'budget_range' => '5l-15l',
            'consent_given' => true,
            'consent_recorded_at' => Carbon::now()->subDays(30),
            'notes' => 'Recent guest notes',
            'ip_address' => '192.168.1.2',
        ]);
        $recentGuestEnquiry->created_at = Carbon::now()->subDays(30);
        $recentGuestEnquiry->save();

        // Run the command
        $this->artisan('privacy:scrub-expired', ['--days' => 180])
            ->expectsOutputToContain('Scanning for guest records')
            ->expectsOutputToContain('Successfully scrubbed and anonymized 1 expired guest enquiry records')
            ->assertSuccessful();

        // Expired record should be scrubbed
        $expiredGuestEnquiry->refresh();
        $this->assertEquals('Anonymized Guest Record', $expiredGuestEnquiry->full_name);
        $this->assertEquals('+910000000000', $expiredGuestEnquiry->phone);
        $this->assertNull($expiredGuestEnquiry->ip_address);

        // Recent record should remain untouched
        $recentGuestEnquiry->refresh();
        $this->assertEquals('Recent Guest Applicant', $recentGuestEnquiry->full_name);
        $this->assertEquals('9876588888', $recentGuestEnquiry->phone);
        $this->assertEquals('192.168.1.2', $recentGuestEnquiry->ip_address);
    }
}
