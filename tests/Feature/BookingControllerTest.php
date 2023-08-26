<?php

namespace Tests\Feature;

use App\Http\Controllers\BookingController;
use App\Models\Booking;
use App\Models\EscapeRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_booking()
    {
        $user = User::factory()->create();
        $escapeRoom = EscapeRoom::factory()
            ->withTimeSlots()
            ->create();
        $data = [
            'escape_room_id' => $escapeRoom->id,
            'time_slot_id' => 1,
        ];

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/bookings', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('bookings', $data);
    }

    public function test_user_should_get_discount_on_birthday()
    {
        $user = User::factory()
            ->withBirthday(now()->format("Y-m-d"))
            ->create();
        $escapeRoom = EscapeRoom::factory()
            ->withTimeSlots()
            ->create();
        $data = [
            'escape_room_id' => $escapeRoom->id,
            'time_slot_id' => $escapeRoom->timeSlots[0]->id,
        ];

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/bookings', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('bookings', [
            'escape_room_id' => $escapeRoom->id,
            'discount_percentage' => BookingController::DISCOUNT_PERCENTAGE,
        ]);
    }

    public function test_two_users_booking_same_time_on_one_empty_slot_left() {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $escapeRoom = EscapeRoom::factory()
            ->withCapacity(1)
            ->withTimeSlots()
            ->create();

        $data = [
            'escape_room_id' => $escapeRoom->id,
            'time_slot_id' => $escapeRoom->timeSlots[0]->id,
        ];

        // First request
        Sanctum::actingAs($user1);

        $response1 = $this->postJson('/api/bookings', $data);

        // Second request
        Sanctum::actingAs($user2);

        $response2 = $this->postJson('/api/bookings', $data);

        $response1->assertStatus(201);
        $response2->assertStatus(409);
    }

    public function test_user_can_view_booking()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()
            ->forUser($user)
            ->create();

        Sanctum::actingAs($user);

        $response = $this->get("/api/bookings");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $booking->id,
            ])
            ->assertJsonCount(1);
    }

    public function test_user_can_cancel_booking()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()
            ->forUser($user)
            ->create();

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/bookings/{$booking->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('bookings', ['id' => $booking->id]);
    }

    public function test_double_booking_attempt_should_fail()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()
            ->forUser($user)
            ->create();

        Sanctum::actingAs($user);

        $this->actingAs($user)
            ->postJson('/api/bookings', [
                'escape_room_id' => $booking->escapeRoom->id,
                'time_slot_id' => $booking->timeSlot->id,
            ])
            ->assertStatus(422)
            ->assertJson([
                'error' => 'You have already booked this escape room and time slot.',
            ]);
    }
}
