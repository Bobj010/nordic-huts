<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_is_redirected_to_login_from_all_dashboards(): void
    {
        $this->get(route('client.bookings'))->assertRedirect(route('login'));
        $this->get(route('manager.index'))->assertRedirect(route('login'));
        $this->get(route('admin.index'))->assertRedirect(route('login'));
    }

    public function test_client_can_view_my_bookings_and_cancel_pending(): void
    {
        $client = User::where('role', 'client')->first();
        $this->actingAs($client);

        // Access dashboard
        $response = $this->get(route('client.bookings'));
        $response->assertStatus(200);
        $response->assertSee('Мои бронирования');
        $response->assertSee($client->name);

        // Cancel pending booking
        $pendingBooking = Booking::where('user_id', $client->id)
            ->where('status', 'pending')
            ->first();

        if ($pendingBooking) {
            $cancelResponse = $this->post(route('client.bookings.cancel', $pendingBooking));
            $cancelResponse->assertRedirect();
            $this->assertEquals('cancelled', $pendingBooking->fresh()->status);
        }
    }

    public function test_client_is_forbidden_from_manager_and_admin_dashboards(): void
    {
        $client = User::where('role', 'client')->first();
        $this->actingAs($client);

        $this->get(route('manager.index'))->assertStatus(403);
        $this->get(route('admin.index'))->assertStatus(403);
    }

    public function test_manager_can_access_manager_desk_and_update_booking_status(): void
    {
        $manager = User::where('role', 'manager')->first();
        $this->actingAs($manager);

        // Access reception desk
        $response = $this->get(route('manager.index'));
        $response->assertStatus(200);
        $response->assertSee('Рабочее место менеджера');
        $response->assertSee('Всего заявок');

        // Update status of a booking
        $booking = Booking::first();
        $updateResponse = $this->patch(route('manager.bookings.status', $booking), [
            'status' => 'confirmed',
        ]);

        $updateResponse->assertRedirect();
        $this->assertEquals('confirmed', $booking->fresh()->status);

        // Manager cannot access admin panel
        $this->get(route('admin.index'))->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard_and_change_user_role(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        // Access admin dashboard
        $response = $this->get(route('admin.index'));
        $response->assertStatus(200);
        $response->assertSee('Панель управляющего комплексом');
        $response->assertSee('Выручка по завершенным');
        $response->assertSee('Номерной фонд');

        // Update a user role
        $client = User::where('role', 'client')->first();
        $updateRoleResponse = $this->patch(route('admin.users.role', $client), [
            'role' => 'manager',
        ]);

        $updateRoleResponse->assertRedirect();
        $this->assertEquals('manager', $client->fresh()->role);
    }
}
