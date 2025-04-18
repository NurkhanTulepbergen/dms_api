<?php

namespace App\Http\Controllers;

use App\Models\GymBooking;
use App\Models\Recovery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GymBookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return GymBooking::with('student')->paginate(10);
    }
    public function showSportsPage()
    {
        $booking = GymBooking::where('user_id', auth()->id())->first();
        $recoveries = Recovery::where('user_id', auth()->id())->get();
        return view('sports.page', compact('booking', 'recoveries'));
    }

    public function store(Request $request)
    {
        // Валидация данных: требуем хотя бы один выбранный день
        $validated = $request->validate([
            'sport' => 'required|string',
            'day'   => 'required|array|min:1',
            'day.*' => 'in:Понедельник,Вторник,Среда,Четверг,Пятница,Суббота,Воскресенье',
            'time'  => 'required|date_format:H:i',
        ]);

        // Проверяем, есть ли уже запись для текущего пользователя
        $existingBooking = GymBooking::where('user_id', Auth::id())->first();
        if ($existingBooking) {
            return redirect()->back()->with('error', 'Вы уже записаны на занятие.');
        }

        // Объединяем выбранные дни в строку (например, "Понедельник, Вторник")
        $daysString = implode(', ', $validated['day']);

        // Создаем новую запись
        $booking = GymBooking::create([
            'user_id' => Auth::id(),
            'sport'   => $validated['sport'],
            'day'     => $daysString,
            'scheduled_time' => $validated['time'],
            'status'  => 'pending',
        ]);

        return redirect()->route('student.personal')
            ->with('successType', 'gym_created')
            ->with('success', 'Вы успешно записаны на занятие.');
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
            return redirect()->route('student.personal')
                ->with('successType', 'gym_canceled')
                ->with('success', 'Вы отменили запись на занятие.');
        }

        return redirect()->back()->with('error', 'У вас нет активной записи.');
    }
    public function recovery(Request $request)
    {
        $request->validate([
            'recoverySport' => 'required|string',
            'recoveryTime' => 'required|date_format:H:i',
        ]);

        // Логика записи на отработку (например, сохранить в таблицу recoveries)
        // Пример:
        Recovery::create([
            'user_id' => auth()->id(),
            'sport' => $request->recoverySport,
            'scheduled_time' => $request->recoveryTime,
        ]);

        return redirect()->route('student.personal')
            ->with('successType', 'recovery_created')
            ->with('success', 'Вы успешно записаны на отработку.');
    }
    public function cancelRecovery($recoveryId)
    {
        $recovery = Recovery::where('id', $recoveryId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$recovery) {
            return redirect()->back()->with('error', 'Отработка не найдена или у вас нет прав для её удаления.');
        }

        $recovery->delete();

        return redirect()->route('student.personal')
            ->with('successType', 'recovery_canceled')
            ->with('success', 'Вы отменили запись на занятие.');
    }



}
