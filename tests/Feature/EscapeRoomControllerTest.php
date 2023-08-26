<?php

namespace Tests\Feature;

use App\Models\EscapeRoom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EscapeRoomControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_list_of_escape_rooms()
    {
        EscapeRoom::factory(3)->create();

        $response = $this->get('/api/escape-rooms');

//        dump($response->getContent());
        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_it_returns_specific_escape_room()
    {
        $escapeRoom = EscapeRoom::factory()->create();

        $response = $this->get("/api/escape-rooms/{$escapeRoom->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $escapeRoom->id,
                'max_participants' => $escapeRoom->max_participants,
                'price' => $escapeRoom->price,
            ]);
    }

    public function test_it_returns_time_slots_for_escape_room()
    {
        $escapeRoom = EscapeRoom::factory()->create();

        $response = $this->get("/api/escape-rooms/{$escapeRoom->id}/time-slots");

        $response->assertStatus(200);
    }
}
