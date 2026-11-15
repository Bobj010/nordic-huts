<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\House;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingLogicTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_cannot_create_booking(): void
    {
        $house = House::first();

        $response = $this->post(route('bookings.store'), [
            'house_id' => $house->id,
            'check_in_date' => Carbon::tomorrow()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
            'guests_count' => 2,
            'guest_name' => 'Гость',
            'guest_phone' => '+7 (999) 000-00-00',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_client_can_create_valid_booking_with_accurate_price_calculation(): void
    {
        $client = User::where('role', 'client')->first();
        $house = House::first(); // Price is known, e.g. 9500 per night

        $checkIn = Carbon::tomorrow()->format('Y-m-d');
        $checkOut = Carbon::tomorrow()->addDays(3)->format('Y-m-d'); // 3 nights

        $response = $this->actingAs($client)->post(route('bookings.store'), [
            'house_id' => $house->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'guests_count' => 2,
            'guest_name' => 'Анна Смирнова',
            'guest_phone' => '+7 (999) 333-44-55',
            'comment' => 'Будем к 19:00',
        ]);

        $response->assertRedirect(route('client.bookings'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('bookings', [
            'user_id' => $client->id,
            'house_id' => $house->id,
            'guests_count' => 2,
            'total_price' => 3 * (float) $house->price_per_night,
            'status' => 'pending',
        ]);
    }

    public function test_validation_rejects_check_out_date_before_or_equal_to_check_in(): void
    {
        $client = User::where('role', 'client')->first();
        $house = House::first();

        $response = $this->actingAs($client)->post(route('bookings.store'), [
            'house_id' => $house->id,
            'check_in_date' => Carbon::tomorrow()->addDays(3)->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->format('Y-m-d'), // Earlier than check-in!
            'guests_count' => 2,
            'guest_name' => 'Иван',
            'guest_phone' => '+7 (999) 111-22-33',
        ]);

        $response->assertSessionHasErrors('check_out_date');
    }

    public function test_booking_fails_if_guests_exceed_house_capacity(): void
    {
        $client = User::where('role', 'client')->first();
        $house = House::where('capacity', 2)->first();

        $response = $this->actingAs($client)->from(route('houses.show', $house->slug))->post(route('bookings.store'), [
            'house_id' => $house->id,
            'check_in_date' => Carbon::tomorrow()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
            'guests_count' => 10, // Exceeds capacity of 2!
            'guest_name' => 'Иван',
            'guest_phone' => '+7 (999) 111-22-33',
        ]);

        $response->assertRedirect(route('houses.show', $house->slug));
        $response->assertSessionHas('error');
    }

    public function test_client_cannot_cancel_another_clients_booking(): void
    {
        $client1 = User::where('role', 'client')->first();
        $client2 = User::where('role', 'client')->skip(1)->first();

        // Create booking belonging to client 2
        $booking = Booking::create([
            'user_id' => $client2->id,
            'house_id' => House::first()->id,
            'check_in_date' => Carbon::tomorrow()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
            'guests_count' => 2,
            'total_price' => 19000,
            'status' => 'pending',
            'guest_name' => $client2->name,
            'guest_phone' => $client2->phone,
        ]);

        // Client 1 tries to cancel client 2's booking
        $response = $this->actingAs($client1)->post(route('client.bookings.cancel', $booking));
        $response->assertStatus(403);
    }

    public function test_manager_can_update_booking_status(): void
    {
        $manager = User::where('role', 'manager')->first();
        $booking = Booking::where('status', 'pending')->first();

        $response = $this->actingAs($manager)->patch(route('manager.bookings.status', $booking), [
            'status' => 'confirmed',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('confirmed', $booking->fresh()->status);
    }
}
