<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_is_redirected_to_login_when_accessing_manager(): void
    {
        $response = $this->get(route('manager.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_to_login_when_accessing_admin(): void
    {
        $response = $this->get(route('admin.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_client_is_forbidden_from_accessing_manager_panel(): void
    {
        $client = User::where('role', 'client')->first();

        $response = $this->actingAs($client)->get(route('manager.index'));
        $response->assertStatus(403);
    }

    public function test_client_is_forbidden_from_accessing_admin_panel(): void
    {
        $client = User::where('role', 'client')->first();

        $response = $this->actingAs($client)->get(route('admin.index'));
        $response->assertStatus(403);
    }

    public function test_manager_can_access_manager_panel_but_not_admin(): void
    {
        $manager = User::where('role', 'manager')->first();

        // Manager can open manager desk
        $response = $this->actingAs($manager)->get(route('manager.index'));
        $response->assertStatus(200);

        // Manager cannot open admin panel
        $adminResponse = $this->actingAs($manager)->get(route('admin.index'));
        $adminResponse->assertStatus(403);
    }

    public function test_admin_can_access_both_admin_and_manager_panels(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.index'));
        $response->assertStatus(200);

        $managerResponse = $this->actingAs($admin)->get(route('manager.index'));
        $managerResponse->assertStatus(200);
    }

    public function test_quick_login_switches_roles_accurately(): void
    {
        // 1. Quick login as manager
        $response = $this->get(route('quick-login', ['role' => 'manager']));
        $response->assertRedirect(route('manager.index'));
        $this->assertAuthenticated();
        $this->assertEquals('manager', auth()->user()->role);

        // 2. Quick login as admin
        $adminResponse = $this->get(route('quick-login', ['role' => 'admin']));
        $adminResponse->assertRedirect(route('admin.index'));
        $this->assertEquals('admin', auth()->user()->role);

        // 3. Quick login as guest logs out
        $guestResponse = $this->get(route('quick-login', ['role' => 'guest']));
        $guestResponse->assertRedirect(route('home'));
        $this->assertGuest();
    }
}
