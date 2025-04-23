<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GymBooking;
use App\Models\Recovery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GymBookingController  extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $bookings = GymBooking::with('student')->paginate(10);
        return response()->json($bookings);
    }

    public function showSportsPage()
    {
        $booking = GymBooking::where('user_id', auth()->id())->first();
        $recoveries = Recovery::where('user_id', auth()->id())->get();

        return response()->json([
            'booking' => $booking,
            'recoveries' => $recoveries,
        ]);
    }

    public function store(Request $request)
    {
        // Валидация данных
        $validated = $request->validate([
            'sport' => 'required|string',
            'day'   => 'required|array|min:1',
            'day.*' => 'in:Понедельник,Вторник,Среда,Четверг,Пятница,Суббота,Воскресенье',
            'time'  => 'required|date_format:H:i',
        ]);

        // Проверяем, есть ли уже запись для текущего пользователя
        $existingBooking = GymBooking::where('user_id', Auth::id())->first();
        if ($existingBooking) {
            return response()->json(['message' => 'Вы уже записаны на занятие.'], 400);
        }

        // Объединяем выбранные дни в строку
        $daysString = implode(', ', $validated['day']);

        // Создаем новую запись
        $booking = GymBooking::create([
            'user_id' => Auth::id(),
            'sport'   => $validated['sport'],
            'day'     => $daysString,
            'scheduled_time' => $validated['time'],
            'status'  => 'pending',
        ]);

        return response()->json([
            'message' => 'Вы успешно записаны на занятие.',
            'booking' => $booking
        ], 201);
    }

    public function confirm(GymBooking $gymBooking)
    {
        if (auth()->id() !== $gymBooking->student_id && !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $gymBooking->confirm();
        return response()->json(['message' => 'Booking confirmed']);
    }

    public function cancel()
    {
        $booking = GymBooking::where('user_id', Auth::id())->first();

        if ($booking) {
            $booking->delete();
            return response()->json(['message' => 'Вы отменили запись на занятие.']);
        }

        return response()->json(['message' => 'У вас нет активной записи.'], 404);
    }

    public function recovery(Request $request)
    {
        $validated = $request->validate([
            'recoverySport' => 'required|string',
            'recoveryTime' => 'required|date_format:H:i',
        ]);

        // Логика записи на отработку
        $recovery = Recovery::create([
            'user_id' => auth()->id(),
            'sport' => $validated['recoverySport'],
            'scheduled_time' => $validated['recoveryTime'],
        ]);

        return response()->json([
            'message' => 'Вы успешно записаны на отработку.',
            'recovery' => $recovery
        ], 201);
    }

    public function cancelRecovery($recoveryId)
    {
        $recovery = Recovery::where('id', $recoveryId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$recovery) {
            return response()->json(['message' => 'Отработка не найдена или у вас нет прав для её удаления.'], 404);
        }

        $recovery->delete();

        return response()->json(['message' => 'Вы отменили запись на занятие.']);
    }
}
