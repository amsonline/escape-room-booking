<?php

namespace Database\Factories;

use App\Models\EscapeRoom;
use App\Models\TimeSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimeSlotFactory extends Factory
{
    protected $model = TimeSlot::class;

    public function definition()
    {
        $startTime = $this->faker->dateTimeThisMonth;
        $endTime = clone $startTime;
        $endTime->add(new \DateInterval('PT1H'));

        return [
            'escape_room_id' => EscapeRoom::factory(),
            'start_time' => $startTime,
            'end_time' => $endTime,
        ];
    }
}
