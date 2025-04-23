<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Building;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    // ✅ Отправка заявки на заселение (только студент)
    public function store(Request $request)
    {
        $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'floor' => 'required|integer',
            'room_id' => 'required|exists:rooms,id',
        ]);

        $room = Room::findOrFail($request->room_id);

        if (!$room->hasAvailableSpace()) {
            return response()->json(['message' => 'No available places in this room'], 422);
        }

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'building_id' => $request->building_id,
            'floor' => $request->floor,
            'room_id' => $request->room_id,
            'status' => 'pending', // pending / approved / rejected
        ]);

        return response()->json(['message' => 'Booking request sent', 'booking' => $booking]);
    }

    // ✅ Просмотр всех заявок (только для менеджера)
    public function index()
    {
        // Фильтруем заявки по статусу "pending"
        $bookings = Booking::with(['user', 'building', 'room'])
            ->where('status', 'pending') // Добавляем условие для фильтрации по статусу
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($bookings);
    }

    // ✅ Принятие заявки
    public function accepted($id)
    {
        $booking = Booking::findOrFail($id);
        $room = $booking->room;

        if (!$room->hasAvailableSpace()) {
            return response()->json(['message' => 'Room is full'], 422);
        }

        $booking->status = 'accepted';
        $booking->save();

        // Увеличиваем количество занятых мест
        $room->increment('occupied_places');

        return response()->json(['message' => 'accepted']);
    }

    // ❌ Отклонение заявки
    public function reject($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = 'rejected';
        $booking->save();

        return response()->json(['message' => 'rejected']);
    }

    // ✅ Получить этажи для выбранного корпуса
    public function getFloors($buildingId)
    {
        $building = Building::findOrFail($buildingId);
        // Извлекаем только этажи, сбрасываем индексы
        $floors = $building->rooms->pluck('floor')->unique()->values();

        return response()->json([
            'floors' => $floors
        ]);
    }

    // ✅ Получить список всех корпусов
    public function getBuildings()
    {
        $buildings = Building::select('id', 'name')->get();

        return response()->json([
            'buildings' => $buildings
        ]);
    }


    // ✅ Получить комнаты для выбранного корпуса и этажа
    public function getRooms($buildingId, $floor)
    {
        $building = Building::findOrFail($buildingId);
        $rooms = $building->rooms()->where('floor', $floor)->get();

        return response()->json([
            'rooms' => $rooms->map(function ($room) {
                return [
                    'id' => $room->id,
                    'room_number' => $room->room_number,
                    'capacity' => $room->capacity,
                    'occupied_places' => $room->occupied_places
                ];
            })
        ]);
    }
}
