<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use App\Models\Document;
use App\Models\DocumentAccessLog;
use App\Models\DocumentConsent;
use App\Models\Enquiry;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentAndConsentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    /**
     * Test customer can view their own case documents repository and consent screen.
     */
    public function test_customer_can_view_own_case_documents_and_consents(): void
    {
        $customer = User::factory()->customer()->create();

        $enquiry = Enquiry::create([
            'reference_code' => 'FD-CASE-202610-2001',
            'user_id' => $customer->id,
            'full_name' => $customer->name,
            'phone' => '9876500001',
            'email' => $customer->email,
            'target_state' => 'MH',
            'target_city' => 'Pune',
            'budget_range' => '5l-15l',
            'consent_given' => true,
            'consent_recorded_at' => now(),
        ]);

        $response = $this->actingAs($customer)->get("/customer/cases/{$enquiry->id}/documents");

        $response->assertStatus(200);
        $response->assertSee('FD-CASE-202610-2001');
        $response->assertSee('Confidential Documents &amp; Sharing Authorizations', false);
    }

    /**
     * IDOR Security Test: Customer cannot view another customer's document vault.
     */
    public function test_customer_cannot_view_documents_of_another_customer(): void
    {
        $customerA = User::factory()->customer()->create();
        $customerB = User::factory()->customer()->create();

        $enquiryB = Enquiry::create([
            'reference_code' => 'FD-CASE-202610-2002',
            'user_id' => $customerB->id,
            'full_name' => $customerB->name,
            'phone' => '9876500002',
            'email' => $customerB->email,
            'target_state' => 'DL',
            'target_city' => 'Delhi',
            'budget_range' => '15l-30l',
            'consent_given' => true,
            'consent_recorded_at' => now(),
        ]);

        $response = $this->actingAs($customerA)->get("/customer/cases/{$enquiryB->id}/documents");

        $response->assertStatus(403);
    }

    /**
     * Test customer can upload a valid PDF document to private storage.
     */
    public function test_customer_can_upload_valid_document_to_private_storage(): void
    {
        $customer = User::factory()->customer()->create();

        $enquiry = Enquiry::create([
            'reference_code' => 'FD-CASE-202610-2003',
            'user_id' => $customer->id,
            'full_name' => $customer->name,
            'phone' => '9876500003',
            'email' => $customer->email,
            'target_state' => 'KA',
            'target_city' => 'Bengaluru',
            'budget_range' => '15l-30l',
            'consent_given' => true,
            'consent_recorded_at' => now(),
        ]);

        $file = UploadedFile::fake()->create('bank_statement.pdf', 500, 'application/pdf');

        $response = $this->actingAs($customer)->post("/customer/cases/{$enquiry->id}/documents", [
            'document' => $file,
            'category' => 'financial',
            'title' => '6-Month HDFC Bank Statement',
            'notes' => 'Current operating balance shown on page 3',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('documents', [
            'enquiry_id' => $enquiry->id,
            'uploaded_by_user_id' => $customer->id,
            'category' => 'financial',
            'title' => '6-Month HDFC Bank Statement',
            'original_filename' => 'bank_statement.pdf',
            'verification_status' => 'pending',
        ]);

        $document = Document::where('enquiry_id', $enquiry->id)->first();
        $this->assertNotNull($document);

        // Verify stored in private local disk
        Storage::disk('local')->assertExists($document->storage_path);

        // Verify upload access log created
        $this->assertDatabaseHas('document_access_logs', [
            'document_id' => $document->id,
            'user_id' => $customer->id,
            'action' => 'upload',
        ]);
    }

    /**
     * Test upload rejects unpermitted executable or non-allowlisted MIME types.
     */
    public function test_document_upload_rejects_unpermitted_mime_types(): void
    {
        $customer = User::factory()->customer()->create();

        $enquiry = Enquiry::create([
            'reference_code' => 'FD-CASE-202610-2004',
            'user_id' => $customer->id,
            'full_name' => $customer->name,
            'phone' => '9876500004',
            'email' => $customer->email,
            'target_state' => 'MH',
            'target_city' => 'Mumbai',
            'budget_range' => '5l-15l',
            'consent_given' => true,
            'consent_recorded_at' => now(),
        ]);

        $dangerousFile = UploadedFile::fake()->create('payload.exe', 100, 'application/x-msdownload');

        $response = $this->actingAs($customer)->post("/customer/cases/{$enquiry->id}/documents", [
            'document' => $dangerousFile,
            'category' => 'other',
            'title' => 'Executable Test',
        ]);

        $response->assertSessionHasErrors('document');
        $this->assertDatabaseMissing('documents', ['title' => 'Executable Test']);
    }

    /**
     * Test customer can download own document and access is recorded in audit logs.
     */
    public function test_customer_can_download_own_document_and_access_is_logged(): void
    {
        $customer = User::factory()->customer()->create();

        $enquiry = Enquiry::create([
            'reference_code' => 'FD-CASE-202610-2005',
            'user_id' => $customer->id,
            'full_name' => $customer->name,
            'phone' => '9876500005',
            'email' => $customer->email,
            'target_state' => 'TN',
            'target_city' => 'Chennai',
            'budget_range' => '5l-15l',
            'consent_given' => true,
            'consent_recorded_at' => now(),
        ]);

        // Place a fake file in storage
        $storedPath = 'cases/'.$enquiry->id.'/pan_card.png';
        Storage::disk('local')->put($storedPath, 'fake-image-binary-data');

        $document = Document::create([
            'enquiry_id' => $enquiry->id,
            'uploaded_by_user_id' => $customer->id,
            'category' => 'identity',
            'title' => 'Applicant PAN Card',
            'original_filename' => 'pan_card.png',
            'storage_path' => $storedPath,
            'disk' => 'local',
            'mime_type' => 'image/png',
            'file_size_bytes' => 1024,
            'verification_status' => 'pending',
        ]);

        $response = $this->actingAs($customer)->get("/customer/documents/{$document->id}/download");

        $response->assertStatus(200);

        // Verify audit log recorded
        $this->assertDatabaseHas('document_access_logs', [
            'document_id' => $document->id,
            'user_id' => $customer->id,
            'action' => 'download',
        ]);
    }

    /**
     * Strict Authorization Test: Customer cannot download another customer's document.
     */
    public function test_customer_cannot_download_document_belonging_to_other_customer(): void
    {
        $customerA = User::factory()->customer()->create();
        $customerB = User::factory()->customer()->create();

        $enquiryB = Enquiry::create([
            'reference_code' => 'FD-CASE-202610-2006',
            'user_id' => $customerB->id,
            'full_name' => $customerB->name,
            'phone' => '9876500006',
            'email' => $customerB->email,
            'target_state' => 'UP',
            'target_city' => 'Varanasi',
            'budget_range' => '5l-15l',
            'consent_given' => true,
            'consent_recorded_at' => now(),
        ]);

        $storedPath = 'cases/'.$enquiryB->id.'/secret_tax_return.pdf';
        Storage::disk('local')->put($storedPath, 'confidential-tax-data');

        $documentB = Document::create([
            'enquiry_id' => $enquiryB->id,
            'uploaded_by_user_id' => $customerB->id,
            'category' => 'financial',
            'title' => 'Confidential ITR Return',
            'original_filename' => 'secret_tax_return.pdf',
            'storage_path' => $storedPath,
            'disk' => 'local',
            'mime_type' => 'application/pdf',
            'file_size_bytes' => 2048,
        ]);

        $response = $this->actingAs($customerA)->get("/customer/documents/{$documentB->id}/download");

        $response->assertStatus(403);

        // Verify download was NOT logged as successful download for Customer A
        $this->assertDatabaseMissing('document_access_logs', [
            'document_id' => $documentB->id,
            'user_id' => $customerA->id,
            'action' => 'download',
        ]);
    }

    /**
     * Test case manager can verify or reject a document.
     */
    public function test_case_manager_can_verify_and_reject_case_document(): void
    {
        $caseManager = User::factory()->caseManager()->create();

        $enquiry = Enquiry::create([
            'reference_code' => 'FD-CASE-202610-2007',
            'full_name' => 'Govind Sharma',
            'phone' => '9876500007',
            'email' => 'govind@example.com',
            'target_state' => 'RJ',
            'target_city' => 'Jaipur',
            'budget_range' => '15l-30l',
            'consent_given' => true,
            'consent_recorded_at' => now(),
        ]);

        $document = Document::create([
            'enquiry_id' => $enquiry->id,
            'category' => 'property',
            'title' => 'Commercial Lease Deed',
            'original_filename' => 'lease_deed.pdf',
            'storage_path' => 'cases/'.$enquiry->id.'/lease_deed.pdf',
            'disk' => 'local',
            'mime_type' => 'application/pdf',
            'file_size_bytes' => 1024,
            'verification_status' => 'pending',
        ]);

        // Staff verifies document
        $response = $this->actingAs($caseManager)->post("/admin/cases/documents/{$document->id}/verify", [
            'verification_status' => 'verified',
            'notes' => 'Commercial lease title and stamp duty verified against Rajasthan registry.',
        ]);

        $response->assertRedirect();
        $document->refresh();

        $this->assertEquals('verified', $document->verification_status);
        $this->assertEquals($caseManager->id, $document->verified_by_user_id);
        $this->assertNotNull($document->verified_at);
    }

    /**
     * Test full consent authorization workflow:
     * 1. Staff creates consent request for specific documents.
     * 2. Customer views pending request on their portal.
     * 3. Customer explicitly grants consent with IP and timestamp recorded.
     */
    public function test_consent_request_and_grant_workflow(): void
    {
        $customer = User::factory()->customer()->create(['name' => 'Nidhi Gupta']);
        $caseManager = User::factory()->caseManager()->create(['name' => 'Sanjay Verma']);

        $category = Category::factory()->create();
        $company = Company::factory()->create(['brand_name' => 'Tea Trails']);
        $opportunity = Opportunity::factory()->published()->create([
            'category_id' => $category->id,
            'company_id' => $company->id,
            'title' => 'Tea Trails Cafe',
        ]);

        $enquiry = Enquiry::create([
            'reference_code' => 'FD-CASE-202610-2008',
            'user_id' => $customer->id,
            'opportunity_id' => $opportunity->id,
            'assigned_to_user_id' => $caseManager->id,
            'full_name' => $customer->name,
            'phone' => '9876500008',
            'email' => $customer->email,
            'target_state' => 'MH',
            'target_city' => 'Pune',
            'budget_range' => '15l-30l',
            'consent_given' => true,
            'consent_recorded_at' => now(),
        ]);

        $doc1 = Document::create([
            'enquiry_id' => $enquiry->id,
            'category' => 'identity',
            'title' => 'PAN Card Copy',
            'original_filename' => 'pan.pdf',
            'storage_path' => 'cases/'.$enquiry->id.'/pan.pdf',
            'disk' => 'local',
            'mime_type' => 'application/pdf',
            'file_size_bytes' => 1024,
            'verification_status' => 'verified',
        ]);

        $doc2 = Document::create([
            'enquiry_id' => $enquiry->id,
            'category' => 'financial',
            'title' => 'Net Worth Certificate',
            'original_filename' => 'networth.pdf',
            'storage_path' => 'cases/'.$enquiry->id.'/networth.pdf',
            'disk' => 'local',
            'mime_type' => 'application/pdf',
            'file_size_bytes' => 2048,
            'verification_status' => 'verified',
        ]);

        // 1. Staff submits consent request to applicant
        $staffResponse = $this->actingAs($caseManager)->post("/admin/cases/{$enquiry->id}/consents/request", [
            'document_ids' => [$doc1->id, $doc2->id],
            'scope_summary' => 'Authorizing transmission of verified PAN and Net Worth Certificate to Tea Trails corporate expansion committee.',
            'opportunity_id' => $opportunity->id,
        ]);

        $staffResponse->assertRedirect();

        $consent = DocumentConsent::where('enquiry_id', $enquiry->id)->first();
        $this->assertNotNull($consent);
        $this->assertEquals(DocumentConsent::STATUS_PENDING, $consent->status);
        $this->assertEquals([$doc1->id, $doc2->id], $consent->included_document_ids);

        // 2. Customer views pending request
        $customerViewResponse = $this->actingAs($customer)->get("/customer/cases/{$enquiry->id}/documents");
        $customerViewResponse->assertStatus(200);
        $customerViewResponse->assertSee('Action Required: Document Sharing Authorizations');
        $customerViewResponse->assertSee('Request to Share Documents with Tea Trails');

        // 3. Customer explicitly grants consent
        $grantResponse = $this->actingAs($customer)->post("/customer/consents/{$consent->id}/grant");
        $grantResponse->assertRedirect();

        $consent->refresh();
        $this->assertEquals(DocumentConsent::STATUS_GRANTED, $consent->status);
        $this->assertNotNull($consent->granted_at);
        $this->assertNotNull($consent->ip_address);

        // 4. Customer revokes consent
        $revokeResponse = $this->actingAs($customer)->post("/customer/consents/{$consent->id}/revoke");
        $revokeResponse->assertRedirect();

        $consent->refresh();
        $this->assertEquals(DocumentConsent::STATUS_REVOKED, $consent->status);
        $this->assertNotNull($consent->revoked_at);
    }
}
