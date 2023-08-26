<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\EscapeRoom;
use App\Models\User;
use App\Models\TimeSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'escape_room_id' => EscapeRoom::factory(),
            'time_slot_id' => TimeSlot::factory(),
            'price' => $this->faker->randomFloat(2, 10, 100),
        ];
    }

    public function forUser(User $user) {
        return $this->state([
            'user_id'   => $user->id,
        ]);
    }
}
