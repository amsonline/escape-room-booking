<?php

namespace App\Http\Controllers;

use App\Models\EscapeRoom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;

class BookingController extends Controller
{
    private const DISCOUNT_PERCENTAGE = 10;

    public function index(): JsonResponse
    {
        $user = Auth::user();
        $bookings = $user->bookings()->with('escapeRoom', 'timeSlot')->get();
        return response()->json($bookings);
    }

    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'escape_room_id' => 'required|exists:escape_rooms,id',
            'time_slot_id' => 'required|exists:time_slots,id',
        ]);

        // Check if the user has already booked this escape room and time slot
        $existingBooking = Booking::where('user_id', $user->id)
            ->where('escape_room_id', $validatedData['escape_room_id'])
            ->where('time_slot_id', $validatedData['time_slot_id'])
            ->first();

        if ($existingBooking) {
            return response()->json(['error' => 'You have already booked this escape room and time slot.'], 422);
        }

        // Check if the escape room is available and has enough capacity
        $escapeRoom = EscapeRoom::findOrFail($validatedData['escape_room_id']);
        $participantsCount = Booking::where('time_slot_id', $validatedData['time_slot_id'])
            ->sum('participants_count');

        if ($participantsCount >= $escapeRoom->max_participants) {
            return response()->json(['error' => 'Escape room is fully booked for this time slot.'], 422);
        }

        // Checking the user's birthday
        $userDateOfBirth = $user->date_of_birth;
        $isBirthday = now()->format('m-d') === $userDateOfBirth->format('m-d');

        $bookingPrice = $escapeRoom->price;

        // Make discount on the birthday
        if ($isBirthday) {
            $discountAmount = (self::DISCOUNT_PERCENTAGE / 100) * $bookingPrice;
            $bookingPrice -= $discountAmount;
        }

        // Create a booking
        $booking = new Booking([
            'user_id' => $user->id,
            'escape_room_id' => $validatedData['escape_room_id'],
            'time_slot_id' => $validatedData['time_slot_id'],
            'price' => $bookingPrice,
        ]);

        $booking->save();

        return response()->json(['message' => 'Booking created successfully']);
    }

    public function destroy($id): JsonResponse
    {
        $user = Auth::user();
        $booking = Booking::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $booking->delete();

        return response()->json(['message' => 'Booking canceled successfully']);
    }
}
