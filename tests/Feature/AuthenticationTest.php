<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use App\Models\Enquiry;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test login view renders correctly.
     */
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Sign In to Franchise Dekho');
        $response->assertSee('Secure Account Access');
    }

    /**
     * Test successful authentication.
     */
    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'email' => 'rahul@example.com',
            'password' => Hash::make('Secret123!'),
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'rahul@example.com',
            'password' => 'Secret123!',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    /**
     * Test authentication rejection for bad password.
     */
    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'rahul@example.com',
            'password' => Hash::make('Secret123!'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'rahul@example.com',
            'password' => 'WrongPassword!',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test deactivated users cannot log in.
     */
    public function test_deactivated_users_cannot_authenticate(): void
    {
        $user = User::factory()->deactivated()->create([
            'email' => 'inactive@example.com',
            'password' => Hash::make('Secret123!'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'inactive@example.com',
            'password' => 'Secret123!',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test registration screen renders.
     */
    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Create Your Franchise Dekho Account');
        $response->assertSee('Entrepreneur Registration');
    }

    /**
     * Test successful registration creates customer account and logs them in.
     */
    public function test_new_users_can_register_as_customers_with_valid_details_and_consent(): void
    {
        $response = $this->post('/register', [
            'name' => 'Vikram Seth',
            'email' => 'vikram@example.com',
            'phone' => '9820098200',
            'state' => 'MH',
            'city' => 'Pune',
            'password' => 'StrongP@ssw0rd',
            'password_confirmation' => 'StrongP@ssw0rd',
            'terms_agreed' => '1',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'vikram@example.com',
            'role' => User::ROLE_CUSTOMER,
            'state' => 'MH',
            'city' => 'Pune',
            'is_active' => true,
        ]);
    }

    /**
     * Test registration strictly requires accepting terms and privacy policy.
     */
    public function test_registration_fails_if_mandatory_terms_not_accepted(): void
    {
        $response = $this->post('/register', [
            'name' => 'Vikram Seth',
            'email' => 'vikram@example.com',
            'phone' => '9820098200',
            'state' => 'MH',
            'city' => 'Pune',
            'password' => 'StrongP@ssw0rd',
            'password_confirmation' => 'StrongP@ssw0rd',
            // Missing terms_agreed
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('terms_agreed');
    }

    /**
     * Test registration rejects weak passwords (min 8 chars, mixed case, numbers).
     */
    public function test_registration_fails_with_weak_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'Vikram Seth',
            'email' => 'vikram@example.com',
            'phone' => '9820098200',
            'state' => 'MH',
            'city' => 'Pune',
            'password' => 'simple',
            'password_confirmation' => 'simple',
            'terms_agreed' => '1',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('password');
    }

    /**
     * Test registration automatically links prior guest enquiries by matching email.
     */
    public function test_prior_guest_enquiries_are_linked_to_user_upon_registration(): void
    {
        // Prior enquiry submitted by guest
        $enquiry = Enquiry::create([
            'reference_code' => Enquiry::generateReferenceCode(),
            'user_id' => null,
            'full_name' => 'Aditi Rao',
            'phone' => '9811122233',
            'email' => 'aditi@example.com',
            'target_state' => 'KA',
            'target_city' => 'Bengaluru',
            'budget_range' => '15l-30l',
            'preferred_timeline' => '1-3-months',
            'commercial_space_status' => 'searching',
            'business_experience' => 'first_time',
            'consent_given' => true,
            'consent_recorded_at' => now(),
            'status' => 'new',
        ]);

        $this->assertNull($enquiry->user_id);

        // Aditi registers account
        $this->post('/register', [
            'name' => 'Aditi Rao',
            'email' => 'aditi@example.com',
            'phone' => '9811122233',
            'state' => 'KA',
            'city' => 'Bengaluru',
            'password' => 'SecurePass123',
            'password_confirmation' => 'SecurePass123',
            'terms_agreed' => '1',
        ]);

        $user = User::where('email', 'aditi@example.com')->first();
        $enquiry->refresh();

        $this->assertEquals($user->id, $enquiry->user_id);
    }

    /**
     * Test enquiry submission associates logged-in user id.
     */
    public function test_enquiry_submission_associates_logged_in_user_id(): void
    {
        $user = User::factory()->customer()->create();

        $response = $this->actingAs($user)->post('/enquiry', [
            'full_name' => $user->name,
            'phone' => '9876543210',
            'email' => $user->email,
            'target_state' => 'MH',
            'target_city' => 'Mumbai',
            'budget_range' => '5l-15l',
            'preferred_timeline' => '1-3-months',
            'commercial_space_status' => 'owned',
            'carpet_area_sqft' => 350,
            'business_experience' => 'existing_retail',
            'consent' => '1',
        ]);

        $response->assertRedirect(route('enquiry.confirmed'));

        $this->assertDatabaseHas('enquiries', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }

    /**
     * Test sign out.
     */
    public function test_authenticated_users_can_log_out(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    /**
     * Test dashboard is protected by auth middleware.
     */
    public function test_guests_are_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    /**
     * Test customer can view own dashboard and enquiries.
     */
    public function test_customer_can_view_own_dashboard_and_enquiries(): void
    {
        $user = User::factory()->customer()->create(['name' => 'Pooja Hegde']);

        $category = Category::factory()->create();
        $company = Company::factory()->create(['brand_name' => 'Naturals Salon']);
        $opportunity = Opportunity::factory()->published()->create([
            'category_id' => $category->id,
            'company_id' => $company->id,
            'title' => 'Naturals Prime Lounge',
        ]);

        $enquiry = Enquiry::create([
            'reference_code' => 'FD-CASE-202610-0099',
            'user_id' => $user->id,
            'opportunity_id' => $opportunity->id,
            'full_name' => $user->name,
            'phone' => '9988776655',
            'email' => $user->email,
            'target_state' => 'TN',
            'target_city' => 'Chennai',
            'budget_range' => '15l-30l',
            'status' => 'new',
            'consent_given' => true,
            'consent_recorded_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Welcome, Pooja Hegde');
        $response->assertSee('FD-CASE-202610-0099');
        $response->assertSee('Naturals Prime Lounge');
        $response->assertSee('Case Specialist Assigned');
    }

    /**
     * Test customer dashboard does not leak enquiries from other customers.
     */
    public function test_customer_cannot_view_enquiries_belonging_to_other_customers(): void
    {
        $customerA = User::factory()->customer()->create(['name' => 'Customer A', 'email' => 'a@example.com']);
        $customerB = User::factory()->customer()->create(['name' => 'Customer B', 'email' => 'b@example.com']);

        $enquiryB = Enquiry::create([
            'reference_code' => 'FD-CASE-SECRET-007',
            'user_id' => $customerB->id,
            'full_name' => 'Customer B',
            'phone' => '9111111111',
            'email' => $customerB->email,
            'target_state' => 'DL',
            'target_city' => 'Delhi',
            'budget_range' => '30l-plus',
            'status' => 'submitted_to_brand',
            'consent_given' => true,
            'consent_recorded_at' => now(),
        ]);

        $response = $this->actingAs($customerA)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertDontSee('FD-CASE-SECRET-007');
    }

    /**
     * Test role middleware blocks unauthorized user roles with 403.
     */
    public function test_role_middleware_enforces_role_restrictions(): void
    {
        \Illuminate\Support\Facades\Route::get('/_test/staff-only', function () {
            return response('Staff Portal');
        })->middleware(['auth', 'role:case_manager,admin']);

        $customer = User::factory()->customer()->create();
        $staff = User::factory()->caseManager()->create();

        // Customer blocked
        $customerResponse = $this->actingAs($customer)->get('/_test/staff-only');
        $customerResponse->assertStatus(403);

        // Case manager authorized
        $staffResponse = $this->actingAs($staff)->get('/_test/staff-only');
        $staffResponse->assertStatus(200);
        $staffResponse->assertSee('Staff Portal');
    }
}
