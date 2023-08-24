<?php

namespace App\Http\Controllers;

use App\Models\EscapeRoom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EscapeRoomController extends Controller
{
    public function index(): JsonResponse
    {
        $escapeRooms = EscapeRoom::all();
        return response()->json($escapeRooms);
    }

    public function show($id): JsonResponse
    {
        $escapeRoom = EscapeRoom::findOrFail($id);
        return response()->json($escapeRoom);
    }

    public function getTimeSlots($id): JsonResponse
    {
        $escapeRoom = EscapeRoom::findOrFail($id);
        $timeSlots = $escapeRoom->timeSlots; // Assuming you have a relationship set up in your model
        return response()->json($timeSlots);
    }
}
