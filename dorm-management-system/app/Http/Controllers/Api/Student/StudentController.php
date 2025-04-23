<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // Импорт правильного Request
use App\Models\User;
use App\Models\Booking;

class StudentController extends Controller
{
    public function name(Request $request) {
        return response()->json([
            'name' => $request->user()->name,
        ]);
    }

    public function allData(Request $request){
        return response()->json(
            $request->user()
        );
    }

    public function myRoomInfo(Request $request){
        $user = $request->user();

        $booking = Booking::with(['building', 'room'])
            ->where('user_id', $user->id)
            ->where('status', 'accepted')
            ->latest()
            ->first();

        if (!$booking) {
            return response()->json(['message' => 'У вас нет подтверждённой брони.'], 404);
        }

        return response()->json([
            'building' => $booking->building->name ?? 'Не указано',
            'floor' => $booking->floor,
            'room' => $booking->room->room_number ?? 'Не указано',
        ]);
    }
}
