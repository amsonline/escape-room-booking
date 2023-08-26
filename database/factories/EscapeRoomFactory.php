<?php

namespace Database\Factories;

use App\Models\EscapeRoom;
use App\Models\TimeSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

class EscapeRoomFactory extends Factory
{
    protected $model = EscapeRoom::class;

    public function definition()
    {
        return [
            'theme' => $this->faker->word,
            'max_participants' => $this->faker->numberBetween(2, 8),
            'price' => $this->faker->randomFloat(2, 20, 100),
        ];
    }

    public function withCapacity(int $capacity) {
        return $this->state([
            'max_participants' => $capacity
        ]);
    }

    public function withTimeSlots()
    {
        return $this->afterCreating(function (EscapeRoom $room) {
            $room->timeSlots()->saveMany(TimeSlot::factory(2)->create());
        });
    }
}

